<?php

declare(strict_types=1);

namespace ninjaknights\arcade\event\gamesession;

use ninjaknights\arcade\gamesession\GameSession;
use pocketmine\event\Event;

final class GameSessionStartEvent extends Event {

    public function __construct(private readonly GameSession $gameSession) {}

    public function getGameSession() : GameSession{
        return $this->gameSession;
    }
}