<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\Image;
use App\FileManagement\UploadImage;
use App\Form\CommentFormType;
use App\Form\TrickCreateFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentRepository;
use App\Repository\VideoRepository;
use App\Repository\TrickRepository;
use DateTimeImmutable;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\SecurityBundle\Security;

class TrickController extends AbstractController
{
    #[Route(
        '/trick/{slug}/{pageNb}',
        name: 'app_trick_detail',
        requirements: [
            'pageNb' => '\d+',
        ]
    )]
    public function index(Request $request, TrickRepository $trickRepository, CommentRepository $commentRepository, EntityManagerInterface $entityManager, string $slug, Security $security, TranslatorInterface $translator, int $pageNb = 0): Response
    {
        $trick = $trickRepository->getTrickBySlug($slug);
        $commentList = $trick->getComments();
        $commentTotal = $trick->getComments()->count([]);
        $limit = 2;
        $limitReached = false;

        if ($commentTotal > $limit * $pageNb) {
            $pageNb++;
        }
        if ($commentTotal <= $limit * $pageNb) {
            $limitReached = true;
        }

        $commentList = $commentRepository->findBy(['trick' => $trick], ['createdAt' => 'DESC'], $limit * $pageNb, 0);

        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setContent($form->get('content')->getData());
            $comment->setUuid(Uuid::v6());
            $comment->setCreatedAt(new DateTimeImmutable);
            $comment->setTrick($trick);
            $comment->setAuthor($security->getUser());

            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('Trick.Comment.Done'));

            return $this->redirectToRoute('app_trick_detail', ['slug' => $trick->getSlug()]);
        }

        return $this->render('trick/index.html.twig', [
            'controller_name' => 'TrickController',
            'trick' => $trick,
            'commentList' => $commentList,
            'form' => $form->createView(),
            'pageNb' => $pageNb,
            'commentTotal' => $commentTotal,
            'limit' => $limit,
            'limitReached' => $limitReached
        ]);
    }

    #[Route('/trickCreate', name: 'app_trick_create', methods: ['GET', 'POST'])]
    public function trickCreate(Request $request, UploadImage $uploadImage, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickCreateFormType::class, $trick);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $trickName = $form->get('name')->getData();
            $trick->setName($trickName);
            $trick->setDescription($form->get('description')->getData());
            $trick->setCreatedAt(new DateTimeImmutable());
            $trick->setCategory($form->get('category')->getData());
            $trick->setUser($this->getUser());
            $trick->setSlug($slugger->slug($trickName));
            $images = $form->get('images')->getData();

            foreach ($images as $image) {
                if ($uploadImage->validateUploadedFile($image)) {
                    $fichier = $uploadImage->saveImage($image, $trick->getSlug());
                    $img = new Image();
                    $img->setFileName($fichier);
                    $img->setMainImage(false);
                    $img->setUuid(Uuid::v6());
                    $trick->addImage($img);
                };
            }
            if ($uploadImage->hasError()) {
                $this->addFlash('errorfile', $uploadImage->getErrorMessages());
            }
            if (!$uploadImage->hasError()) {
                $entityManager->persist($trick);
                $entityManager->flush();
                $this->addFlash('success', 'Trick.Created');
                return $this->redirectToRoute('app_trick_detail', ['slug' => $trick->getSlug()]);
            }
        }

        return $this->render('trick/create.html.twig', [
            'controller_name' => 'TrickController',
            'trick'           => $trick,
            'form'            => $form->createView(),
        ]);
    }
}
