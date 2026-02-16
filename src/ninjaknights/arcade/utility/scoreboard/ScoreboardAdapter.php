<?php

declare(strict_types=1);

namespace ninjaknights\arcade\utility\scoreboard;

use ninjaknights\arcade\player\ArcadePlayer;

interface ScoreboardAdapter {

	public function setTitle(ArcadePlayer $player, string $title) : void;

	public function setLine(ArcadePlayer $player, int $line, string $text) : void;

	public function clear(ArcadePlayer $player) : void;
}
