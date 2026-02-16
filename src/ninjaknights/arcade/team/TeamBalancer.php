<?php

declare(strict_types=1);

namespace ninjaknights\arcade\team;

use ninjaknights\arcade\player\ArcadePlayer;
use function count;

final class TeamBalancer {

	/**
	 * @param array<string, Team>      $teams
	 * @param array<int, ArcadePlayer> $players
	 */
	public function balance(array $teams, array $players, TeamManager $teamManager) : void{
		if(count($teams) === 0){
			return;
		}

		foreach($players as $player){
			$smallestTeam = null;
			foreach($teams as $team){
				if($smallestTeam === null || $team->size() < $smallestTeam->size()){
					$smallestTeam = $team;
				}
			}

			if($smallestTeam instanceof Team){
				$teamManager->assignPlayer($player, $smallestTeam->getId());
			}
		}
	}
}
