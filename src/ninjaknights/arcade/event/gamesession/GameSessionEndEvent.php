<?php

declare(strict_types=1);

namespace ninjaknights\arcade\event\gamesession;

use ninjaknights\arcade\gamesession\GameSession;
use pocketmine\event\Event;

final class GameSessionEndEvent extends Event {

    public function __construct(
        private readonly GameSession $gameSession,
        private readonly string $reason
    ) {}

    public function getGameSession() : GameSession{
        return $this->gameSession;
    }

    public function getReason() : string{
        return $this->reason;
    }
}