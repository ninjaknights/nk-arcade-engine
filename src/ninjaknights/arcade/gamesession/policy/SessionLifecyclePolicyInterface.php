<?php

declare(strict_types=1);

namespace ninjaknights\arcade\gamesession\policy;

use ninjaknights\arcade\gamesession\GameSession;
use ninjaknights\arcade\gamesession\GameState;
use ninjaknights\arcade\player\ArcadePlayer;

interface SessionLifecyclePolicyInterface {

    public function canJoin(GameSession $gameSession, ArcadePlayer $player) : bool;

    public function canTransition(GameSession $gameSession, GameState $from, GameState $to) : bool;

    public function canStart(GameSession $gameSession) : bool;

    public function shouldAutoCloseWhenEmpty(GameSession $gameSession) : bool;

    public function shouldCloseForIdle(GameSession $gameSession, int $currentUnixTime) : bool;
}