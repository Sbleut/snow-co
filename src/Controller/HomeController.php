<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/')]
    public function homepage(TrickRepository $trickRepository) : Response
    {
        $tricklist = $trickRepository->findAll();
        return $this->render('home/home.html.twig', [
            'title' => 'SnowTrick',
            'tricks' => $tricklist
        ]);
    } 

    #[Route('/browse/{slug}')]
    public function browse(string $slug): Response
    {
        return new Response('Browse : '.$slug);
    }
}