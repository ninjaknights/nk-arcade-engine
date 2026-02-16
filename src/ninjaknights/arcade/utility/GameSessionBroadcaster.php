<?php

declare(strict_types=1);

namespace ninjaknights\arcade\utility;

use ninjaknights\arcade\gamesession\GameSession;

final class GameSessionBroadcaster {

	public function broadcast(GameSession $gameSession, string $message) : void{
		foreach($gameSession->getPlayers() as $arcadePlayer){
			$player = $arcadePlayer->getPlayer();
			if($player->isOnline()){
				$player->sendMessage($message);
			}
		}
	}
}
