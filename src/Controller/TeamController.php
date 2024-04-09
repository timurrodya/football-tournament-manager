<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form;
use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class TeamController extends AbstractController
{
    private TeamRepository $repository;

    public function __construct(TeamRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/teams/", name="teams")
     */
    public function index(Request $request): Response
    {
        $team = new Team;

        $form = $this->createForm(Form\Team::class, $team);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $team = $form->getData();
            $this->repository->add($team, true);

            return $this->redirectToRoute('teams');
        }
        $teams = $this->repository->findAll();

        return $this->renderForm('pages/teams.html.twig', [
            'teams' => $teams,
            'form'  => $form,
        ]);
    }

    /**
     * @Route("/team/{id}/delete", name="team_delete")
     */
    public function delete(Team $team, Request $request, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $submittedToken = $request->query->get('token');

        if (! $csrfTokenManager->isTokenValid(new CsrfToken('delete' . $team->getId(), $submittedToken))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token.');
        }

        $this->repository->remove($team, true);

        return $this->redirectToRoute('teams');
    }
}
