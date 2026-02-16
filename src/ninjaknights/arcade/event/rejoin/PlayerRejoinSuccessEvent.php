<?php

declare(strict_types=1);

namespace ninjaknights\arcade\event\rejoin;

use ninjaknights\arcade\gamesession\GameSession;
use ninjaknights\arcade\player\ArcadePlayer;
use pocketmine\event\Event;

final class PlayerRejoinSuccessEvent extends Event {

    public function __construct(
        private readonly ArcadePlayer $player,
        private readonly GameSession $gameSession
    ) {}

    public function getPlayer() : ArcadePlayer{
        return $this->player;
    }

    public function getGameSession() : GameSession{
        return $this->gameSession;
    }
}