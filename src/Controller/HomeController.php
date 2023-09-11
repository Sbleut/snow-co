<?php

namespace App\Controller;

use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/{pageNb}')]
    public function homepage(TrickRepository $trickRepository, ImageRepository $imageRepository, int $pageNb = 0 ) : Response
    {
        $tricktotal= $trickRepository->getTotalTricks();
        $limit = 15;
        $limitReached =false;
        if($tricktotal > $limit * $pageNb){
            $pageNb++;
        }
        if($tricktotal < $limit * $pageNb){
            $limitReached = true;
        }
        $tricklist = $trickRepository->getAllTricks($pageNb, $limit);
        $trickThumbNails = $imageRepository->findAllWithMain();

        return $this->render('home/home.html.twig', [
            'title' => 'SnowTrick',
            'tricks' => $tricklist,
            'trickImage'=> $trickThumbNails,
            'pageNb'=>$pageNb,
            'tricktotal'=>$tricktotal,
            'limit'=>$limit,
            'limitReached'=>$limitReached
        ]);
    } 

    #[Route('/browse/{slug}')]
    public function browse(string $slug): Response
    {
        return new Response('Browse : '.$slug);
    }
}