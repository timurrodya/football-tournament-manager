<?php

namespace App\Services;

use App\Entity\Game;
use App\Entity\Team;
use App\Entity\Tournament;
use App\Repository\GameRepository;
use DateTime;
use Doctrine\Common\Collections\Collection;

class ScheduleBuilder
{
    public Tournament $tournament;

    private array $teams;

    private const FORMAT_DATE = 'Y-m-d';

    private const MATCHES_PER_DAY = 4;

    private GameRepository $gameRepository;

    public function __construct(GameRepository $gameRepository)
    {
        $this->gameRepository = $gameRepository;
    }

    public function addTournament(Tournament $tournament): ScheduleBuilder
    {
        $this->tournament = $tournament;

        return $this;
    }

    public function addTeams(Collection $teams): ScheduleBuilder
    {
        $this->teams = $teams->toArray();

        return $this;
    }

    public function generate(): ScheduleBuilder
    {
        $teams = $this->teams;
        if (count($teams) % 2 != 0) {
            $teams[] = null;
        }
        $away = array_splice($teams, (count($teams) / 2));
        $home = $teams;
        $today = (new DateTime);
        $currentDate = $today->format(self::FORMAT_DATE);
        $todayTeams = [];
        $todayTeams[$currentDate] = [];
        $round = [];
        $round[$currentDate] = [];
        $isModify = false;
        for ($i = 0; $i < count($home) + count($away) - 1; $i++) {
            for ($j = 0; $j < count($home); $j++) {
                if ($home[$j] && $away[$j]) {
                    if (count($round[$currentDate]) === self::MATCHES_PER_DAY) {
                        $today->modify('+1 day');
                        $currentDate = $today->format(self::FORMAT_DATE);
                        if (! isset($round[$currentDate])) {
                            $round[$currentDate] = [];
                        }
                        if (! isset($todayTeams[$currentDate])) {
                            $todayTeams[$currentDate] = [];
                        }
                        $isModify = true;
                    }

                    if (in_array($home[$j], $todayTeams[$currentDate])
                        || in_array($away[$j], $todayTeams[$currentDate])
                    ) {
                        $nextDay = $today;
                        $nextDay->modify('+1 day');
                        $round[$nextDay->format(self::FORMAT_DATE)][] = [$home[$j], $away[$j]];

                        $this->saveGame($nextDay, $away[$j], $home[$j]);

                        $todayTeams[$nextDay->format(self::FORMAT_DATE)] = [$home[$j], $away[$j]];
                    }

                    $this->saveGame($today, $away[$j], $home[$j]);

                    $round[$currentDate][] = [$home[$j], $away[$j]];
                    $todayTeams[$currentDate] = [$home[$j], $away[$j]];
                }
            }
            if (! $isModify) {
                $today->modify('+1 day');
                if (! isset($round[$currentDate])) {
                    $round[$currentDate] = [];
                }
                if (! isset($todayTeams[$currentDate])) {
                    $todayTeams[$currentDate] = [];
                }
            }
            $isModify = false;
            if (count($home) + count($away) - 1 > 2) {
                $array = array_splice($home, 1, 1);
                array_unshift($away, array_shift($array));
                $home[] = array_pop($away);
            }
        }

        return $this;
    }

    private function saveGame(DateTime $day, Team $awayTeam, Team $homeTeam): void
    {
        $game = new Game;
        $game->setDay($day);
        $game->setTeamAway($awayTeam);
        $game->setTeamHome($homeTeam);
        $game->setTournament($this->tournament);
        $this->gameRepository->add($game, true);
    }
}
