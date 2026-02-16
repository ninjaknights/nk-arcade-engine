<?php

declare(strict_types=1);

namespace ninjaknights\arcade\event\gamesession;

use ninjaknights\arcade\gamesession\GameSession;
use ninjaknights\arcade\player\ArcadePlayer;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;

final class PlayerJoinGameSessionEvent extends Event implements Cancellable {
    use CancellableTrait;

    public function __construct(
        private readonly GameSession $gameSession,
        private readonly ArcadePlayer $player
    ) {}

    public function getGameSession() : GameSession{
        return $this->gameSession;
    }

    public function getPlayer() : ArcadePlayer{
        return $this->player;
    }
}