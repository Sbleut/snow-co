<?php

namespace App\Controller;

use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route(
        '/{pageNb}',
        name: 'homepage',
        requirements: ['pageNb' => '\d+']
    )]
    public function homepage(TrickRepository $trickRepository, ImageRepository $imageRepository, int $pageNb = 0 ) : Response
    {
        $tricktotal= $trickRepository->count([]);
        $limit = 15;
        $limitReached =false;
        
        if($tricktotal > $limit * $pageNb){
            $pageNb++;
        }
        if($tricktotal <= $limit * $pageNb){
            $limitReached = true;
        }  
        
       
        $tricklist = $trickRepository->findBy([], [], $limit*$pageNb, 0);
        $trickThumbNails = $imageRepository->findAllWithMain();
        dump($tricklist, $limitReached,  $pageNb, $limit*$pageNb, $limit);

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