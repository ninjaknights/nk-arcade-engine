<?php

declare(strict_types=1);

namespace ninjaknights\arcade\event\rejoin;

use ninjaknights\arcade\gamesession\GameSession;
use ninjaknights\arcade\player\ArcadePlayer;
use pocketmine\event\Event;

final class PlayerRejoinFailedEvent extends Event {

    public const NO_RECOVERABLE_SESSION = 'NO_RECOVERABLE_SESSION';
    public const SESSION_NOT_FOUND = 'SESSION_NOT_FOUND';
    public const ATTEMPT_CANCELLED = 'ATTEMPT_CANCELLED';
    public const SESSION_REJECTED_PLAYER = 'SESSION_REJECTED_PLAYER';

    public function __construct(
        private readonly ArcadePlayer $player,
        private readonly ?GameSession $gameSession,
        private readonly string $reason
    ) {}

    public function getPlayer() : ArcadePlayer{
        return $this->player;
    }

    public function getGameSession() : ?GameSession{
        return $this->gameSession;
    }

    public function getReason() : string{
        return $this->reason;
    }
}