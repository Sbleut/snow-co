<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\SecurityBundle\Security;

class TrickController extends AbstractController
{
    #[Route('/trick/{slug}', name: 'app_trick_detail')]
    public function index(Request $request, TrickRepository $trickRepository, ImageRepository $imageRepository, EntityManagerInterface $entityManager, string $slug, Security $security, TranslatorInterface $translator): Response
    {
        $trick = $trickRepository->getTrickBySlug($slug);
        $trickImages = $imageRepository->findAllByTrickId($trick->getId());
        $commentList = $trick->getComments();

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

            return $this->redirectToRoute('app_trick_detail', ['slug'=> $trick->getSlug()]);
        }

        return $this->render('trick/index.html.twig', [
            'controller_name' => 'TrickController',
            'trick'=> $trick,
            'images'=> $trickImages,
            'commentList'=>$commentList,
            'form'=> $form->createView()
        ]);
    }
}
