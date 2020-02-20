<?php

namespace App\Controller;

use App\Repository\MovieRepository;
use App\Service\AllocineService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{

	/**
	 * @Route("/movies", name="movie", methods={"GET"})
	 *
	 * @param MovieRepository $movieRepository
	 * @return Response
	 */
    public function index(MovieRepository $movieRepository): Response
    {
		return $this->json(
			['movies' => $movieRepository->findAll()], 200, [], ['groups' => 'group1']
		);
    }
}
