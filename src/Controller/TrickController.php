<?php

namespace App\Controller;

use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    #[Route('/trick/{slug}', name: 'app_trick_detail')]
    public function index(TrickRepository $trickRepository, ImageRepository $imageRepository, string $slug): Response
    {
        $trick = $trickRepository->getTrickBySlug($slug);
        $trickImages = $imageRepository->findAllByTrickId($trick->getId());

        return $this->render('trick/index.html.twig', [
            'controller_name' => 'TrickController',
            'trick'=> $trick,
            'images'=> $trickImages
        ]);
    }
}
