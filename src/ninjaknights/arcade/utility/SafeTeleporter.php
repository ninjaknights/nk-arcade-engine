<?php

declare(strict_types=1);

namespace ninjaknights\arcade\utility;

use ninjaknights\arcade\gamesession\GameSession;
use pocketmine\world\Position;

final class SafeTeleporter {

	public function teleportGameSessionPlayers(GameSession $gameSession, Position $position) : void{
		foreach($gameSession->getPlayers() as $arcadePlayer){
			$player = $arcadePlayer->getPlayer();
			if($player->isOnline()){
				$player->teleport($position);
			}
		}
	}
}
