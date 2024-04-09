<?php

namespace App\Entity;

use App\Repository\GameRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 */
class Game
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class, inversedBy="games")
     * @ORM\JoinColumn(nullable=false)
     */
    private $team_home;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class, inversedBy="gsmes_away")
     * @ORM\JoinColumn(nullable=false)
     */
    private $team_away;

    /**
     * @ORM\Column(type="date")
     */
    private $day;

    /**
     * @ORM\ManyToOne(targetEntity=Tournament::class, inversedBy="games")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tournament;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeamHome(): ?Team
    {
        return $this->team_home;
    }

    public function setTeamHome(?Team $team_home): self
    {
        $this->team_home = $team_home;

        return $this;
    }

    public function getTeamAway(): ?Team
    {
        return $this->team_away;
    }

    public function setTeamAway(?Team $team_away): self
    {
        $this->team_away = $team_away;

        return $this;
    }

    public function getDay(): ?DateTimeInterface
    {
        return $this->day;
    }

    public function setDay(DateTimeInterface $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getTournament(): ?Tournament
    {
        return $this->tournament;
    }

    public function setTournament(?Tournament $tournament): self
    {
        $this->tournament = $tournament;

        return $this;
    }
}
