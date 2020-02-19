<?php

namespace App\Controller;

use App\Service\AllocineService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    /**
     * @Route("/movies", name="movie")
     */
    public function index(AllocineService $allocine)
    {
		dd($allocine->movies());
        return $this->render('movie/index.html.twig', [
            'controller_name' => 'MovieController',
        ]);
    }
}
