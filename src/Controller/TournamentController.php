<?php

namespace App\Controller;

use App\Entity\Tournament;
use App\Form;
use App\Repository\TournamentRepository;
use App\Services\ScheduleBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class TournamentController extends AbstractController
{
    private TournamentRepository $tournamentRepository;

    private ScheduleBuilder $builder;

    public function __construct(TournamentRepository $tournamentRepository, ScheduleBuilder $builder)
    {
        $this->tournamentRepository = $tournamentRepository;
        $this->builder = $builder;
    }

    /**
     * @Route("/tournaments/", name="tournaments")
     */
    public function index(Request $request): Response
    {
        $tournament = new Tournament;

        $form = $this->createForm(Form\Tournament::class, $tournament);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $tournament = $form->getData();

            $this->tournamentRepository->add($tournament, true);
            $this->builder->addTournament($tournament)->addTeams($tournament->getTeam())->generate();

            return $this->redirectToRoute('tournaments');
        }
        $tournaments = $this->tournamentRepository->findAll();

        return $this->renderForm('pages/tournaments.html.twig', [
            'tournaments' => $tournaments,
            'form'        => $form,
        ]);
    }

    /**
     * @Route("/tournament/{slug}", name="tournament")
     */
    public function tournament(Tournament $tournament): Response
    {
        $games = $tournament->getGames();

        // dd($games);

        return $this->render('pages/tournament.html.twig', [
            'tournament' => $tournament,
            'games'      => $games,
        ]);
    }

    /**
     * @Route("/tournament/{slug}/delete", name="tournament_delete")
     */
    public function delete(Tournament $tournament, Request $request, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $submittedToken = $request->query->get('token');
        if (! $csrfTokenManager->isTokenValid(new CsrfToken('delete' . $tournament->getId(), $submittedToken))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token.');
        }

        $this->tournamentRepository->remove($tournament, true);

        return $this->redirectToRoute('tournaments');
    }
}
