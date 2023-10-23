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
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
            $comment->setCreatedAt(new DateTimeImmutable());
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
    #[IsGranted('ROLE_USER')]
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

            foreach ($trick->getVideos() as $video) {
                $video->setUuid(Uuid::v6());
            }

            // VErification embeded link youtube Regex
            // Error Message
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

    #[Route(
        '/trick/update/{slug}',
        name: 'app_trick_update',
        methods: ['GET', 'POST'],
    )]
    #[IsGranted('ROLE_USER')]
    public function update(Request $request, TrickRepository $trickRepository, EntityManagerInterface $entityManager, string $slug, UploadImage $uploadImage, SluggerInterface $slugger): Response
    {

        $trick = $trickRepository->getTrickBySlug($slug);
        if (!$trick) {
            throw $this->createNotFoundException("This trick doesn't exist");
        }

        $form = $this->createForm(TrickCreateFormType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trickName = $form->get('name')->getData();
            $trick->setName($trickName);
            $trick->setDescription($form->get('description')->getData());
            $trick->setUpdatedAt(new DateTimeImmutable());
            $trick->setSlug($slugger->slug($trickName));
            $trick->setCategory($form->get('category')->getData());
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

            foreach ($trick->getVideos() as $video) {
                $video->setUuid(Uuid::v6());
            }

            if (!$uploadImage->hasError()) {
                $entityManager->persist($trick);
                $entityManager->flush();
                $this->addFlash('success', 'Trick.Updated');
                return $this->redirectToRoute('app_trick_detail', ['slug' => $trick->getSlug()]);
            }
        }

        return $this->render('trick/update.html.twig', [
            'controller_name' => 'TrickController',
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    #[Route(
        '/main/image/{uuid}',
        name: 'app_main_image',
        methods: ['GET', 'POST'],
    )]
    #[IsGranted('ROLE_USER')]
    public function setMainImage($uuid, ImageRepository $imageRepository, EntityManagerInterface $manager)
    {

        $image = $imageRepository->findOneBy(['uuid' => $uuid], []);
        $actualMain = $imageRepository->findOneBy(['mainImage' => true], []);

        $actualMain->setMainImage(false);
        $image->setMainImage(true);

        $manager->persist($image);
        $manager->flush();
        return $this->redirectToRoute('app_trick_update', ['slug' => $image->getTrick()->getSlug()]);

    }

    #[Route(
        '/delete/image/{uuid}',
        name: 'app_delete_image',
        methods: ['POST','DELETE'],
    )]
    #[IsGranted('ROLE_USER')]
    public function deleteImage(Request $request, $uuid, ImageRepository $imageRepository, EntityManagerInterface $manager)
    {

        $image = $imageRepository->findOneBy(['uuid' => $uuid], []);

        $data = $request->get('_token');
        
        if ($this->isCsrfTokenValid('delete' . $image->getUuid(), $data)) {
            unlink('uploads/image/' . $image->getFileName());

            $manager->remove($image);
            $manager->flush();
            return $this->redirectToRoute('app_trick_update', ['slug' => $image->getTrick()->getSlug()]);
        }
        
        $this->addFlash('error', 'Token.Error');

        return $this->redirectToRoute('app_homepage');
    }

    #[Route(
        '/trick/delete/{slug}',
        name: 'app_delete_trick',
        methods: ['POST', 'DELETE'],
    )]
    #[IsGranted('ROLE_USER')]
    public function deleteTrick(Request $request, $slug, EntityManagerInterface $manager, TrickRepository $trickRepository)
    {
        $data = $request->get('_token');
        $trick = $trickRepository->getTrickBySlug($slug);
        if (!$trick) {
            throw $this->createNotFoundException("This trick doesn't exist");
        }

        
        if ($this->isCsrfTokenValid('delete' . $trick->getSlug(), $data)) {
            foreach ($trick->getImages() as $image) {
                unlink('uploads/image/' . $image->getFileName());
            }
    
            $manager->remove($trick);
            $manager->flush();
    
            $this->addFlash('success', 'Trick.Deleted');
    
            return $this->redirectToRoute('homepage');

        }
        
        $this->addFlash('error', 'Token.error');
    
        return $this->redirectToRoute('homepage');
        
    }

}
