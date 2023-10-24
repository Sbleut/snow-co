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
    /**
     * HomePage manage to give all variables needed to display homepage
     *
     * @param TrickRepository $trickRepository Instance of tickrrepository to fetch Trick object
     * @param integer $pageNb Current page to display a given number of tricks
     * @return Response
     */


    public function homepage(TrickRepository $trickRepository, int $pageNb= 0): Response
    {
        $tricktotal = $trickRepository->count([]);
        $limit = 15;
        $limitReached = false;

        if ($tricktotal > $limit * $pageNb) {
            $pageNb++;
        }
        if ($tricktotal <= $limit * $pageNb) {
            $limitReached = true;
        }

        $tricklist = $trickRepository->findBy([], [], $limit * $pageNb, 0);

        return $this->render('home/home.html.twig', [
            'title'         => 'SnowTrick',
            'tricks'        => $tricklist,
            'pageNb'        => $pageNb,
            'tricktotal'    => $tricktotal,
            'limit'         => $limit,
            'limitReached'  => $limitReached,
        ]);
    }

}
