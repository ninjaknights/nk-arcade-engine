<?php

declare(strict_types=1);

namespace ninjaknights\arcade\team;

use ninjaknights\arcade\player\ArcadePlayer;

final class TeamManager {

	/** @var array<string, Team> */
	private array $teams = [];

	/** @var array<string, string> */
	private array $playerTeamMap = [];

	public function registerTeam(Team $team) : void{
		$this->teams[$team->getId()] = $team;
	}

	public function getTeam(string $teamId) : ?Team{
		return $this->teams[$teamId] ?? null;
	}

	/**
	 * @return array<string, Team>
	 */
	public function getTeams() : array{
		return $this->teams;
	}

	public function assignPlayer(ArcadePlayer $player, string $teamId) : bool{
		$team = $this->getTeam($teamId);
		if($team === null){
			return false;
		}

		$currentTeam = $this->getPlayerTeam($player);
		if($currentTeam !== null){
			$currentTeam->removeMember($player);
		}

		$team->addMember($player);
		$this->playerTeamMap[$player->getUniqueId()] = $teamId;
		return true;
	}

	public function getPlayerTeam(ArcadePlayer $player) : ?Team{
		$teamId = $this->playerTeamMap[$player->getUniqueId()] ?? null;
		if($teamId === null){
			return null;
		}

		return $this->teams[$teamId] ?? null;
	}

	public function removePlayer(ArcadePlayer $player) : void{
		$team = $this->getPlayerTeam($player);
		if($team !== null){
			$team->removeMember($player);
		}

		unset($this->playerTeamMap[$player->getUniqueId()]);
	}
}
