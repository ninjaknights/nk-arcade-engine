<?php

declare(strict_types=1);

namespace ninjaknights\arcade\event\gamesession;

use ninjaknights\arcade\gamesession\GameSession;
use ninjaknights\arcade\gamesession\GameState;
use pocketmine\event\Event;

final class GameSessionStateChangeEvent extends Event {

    public function __construct(
        private readonly GameSession $gameSession,
        private readonly GameState $oldState,
        private readonly GameState $newState
    ) {}

    public function getGameSession() : GameSession{
        return $this->gameSession;
    }

    public function getOldState() : GameState{
        return $this->oldState;
    }

    public function getNewState() : GameState{
        return $this->newState;
    }
}