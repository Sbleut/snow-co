<?php

namespace App\Controller;

use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/')]
    public function homepage(TrickRepository $trickRepository, ImageRepository $imageRepository) : Response
    {
        $tricktotal= $trickRepository->getTotalTricks();
        $limit = 15;
        $pageNb = 1;
        $offset = ceil($tricktotal/ $limit);
        $tricklist = $trickRepository->getAllTricks($offset, $limit);
        $trickThumbNails = $imageRepository->findAllWithMain();
        return $this->render('home/home.html.twig', [
            'title' => 'SnowTrick',
            'tricks' => $tricklist,
            'trickImage'=> $trickThumbNails,
            'pageNb'=>$pageNb,
            'tricktotal'=>$tricktotal,
        ]);
    } 

    #[Route('/browse/{slug}')]
    public function browse(string $slug): Response
    {
        return new Response('Browse : '.$slug);
    }
}