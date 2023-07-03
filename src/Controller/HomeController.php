<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/')]
    public function homepage() : Response
    {
        return $this->render('blog/homepage.html.twig', [
            'title' => 'HomePage',
            'tricks' => [
                0 => '360',
                1 => 'Backflip'
            ]
        ]);
    } 

    #[Route('/browse/{slug}')]
    public function browse(string $slug): Response
    {
        return new Response('Browse : '.$slug);
    }
}