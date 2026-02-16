<?php

declare(strict_types=1);

namespace ninjaknights\arcade\rejoin;

use ninjaknights\arcade\gamesession\GameSession;
use ninjaknights\arcade\player\ArcadePlayer;

interface RejoinManagerInterface {

    public function markRecoverable(ArcadePlayer $player, GameSession $gameSession) : void;

    public function clearRecoverable(string $playerUuid) : void;

    public function canRejoin(ArcadePlayer $player) : bool;

    public function getRecoverableSessionId(string $playerUuid) : ?string;

    public function attemptRejoin(ArcadePlayer $player) : bool;

    public function clearSession(string $sessionId) : void;
}