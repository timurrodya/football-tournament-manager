<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass=TeamRepository::class)
 */
class Team
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Tournament::class, mappedBy="team")
     */
    private $tournament;

    /**
     * @ORM\OneToMany(targetEntity=Game::class, mappedBy="team_home", orphanRemoval=true)
     */
    private $games;

    /**
     * @ORM\OneToMany(targetEntity=Game::class, mappedBy="team_away", orphanRemoval=true)
     */
    private $gsmes_away;

    public function __construct()
    {
        $this->tournament = new ArrayCollection;
        $this->games = new ArrayCollection();
        $this->gsmes_away = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Tournament>
     */
    public function getTournament(): Collection
    {
        return $this->tournament;
    }

    public function addTournament(Tournament $tournament): self
    {
        if (! $this->tournament->contains($tournament)) {
            $this->tournament[] = $tournament;
            $tournament->addTeam($this);
        }

        return $this;
    }

    public function removeTournament(Tournament $tournament): self
    {
        if ($this->tournament->removeElement($tournament)) {
            $tournament->removeTeam($this);
        }

        return $this;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('name', new NotBlank);
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->setTeamHome($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->removeElement($game)) {
            // set the owning side to null (unless already changed)
            if ($game->getTeamHome() === $this) {
                $game->setTeamHome(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGsmesAway(): Collection
    {
        return $this->gsmes_away;
    }

    public function addGsmesAway(Game $gsmesAway): self
    {
        if (!$this->gsmes_away->contains($gsmesAway)) {
            $this->gsmes_away[] = $gsmesAway;
            $gsmesAway->setTeamAway($this);
        }

        return $this;
    }

    public function removeGsmesAway(Game $gsmesAway): self
    {
        if ($this->gsmes_away->removeElement($gsmesAway)) {
            // set the owning side to null (unless already changed)
            if ($gsmesAway->getTeamAway() === $this) {
                $gsmesAway->setTeamAway(null);
            }
        }

        return $this;
    }
}
