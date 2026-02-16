<?php

declare(strict_types=1);

namespace ninjaknights\arcade\event\gamesession;

use ninjaknights\arcade\gamesession\GameSession;
use ninjaknights\arcade\gamesession\SessionResult;
use pocketmine\event\Event;

final class GameSessionEndEvent extends Event {

	public function __construct(
		private readonly GameSession $gameSession,
		private readonly SessionResult $result
	) {}

	public function getGameSession() : GameSession{
		return $this->gameSession;
	}

	public function getResult() : SessionResult{
		return $this->result;
	}

	public function getReason() : string{
		return $this->result->getRawReason();
	}
}
