<?php

namespace App\Controller;

use App\Repository\TournamentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    private TournamentRepository $repository;

    public function __construct(TournamentRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $tournaments = $this->repository->findAll();

        return $this->render('pages/index.html.twig', [
            'tournaments' => $tournaments,
        ]);
    }
}
