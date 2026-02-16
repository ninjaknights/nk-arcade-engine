<?php

declare(strict_types=1);

namespace ninjaknights\arcade\gamesession;

use ninjaknights\arcade\player\ArcadePlayer;

interface SessionLocatorInterface {

	public function findById(string $sessionId) : ?GameSession;

	public function findByPlayer(ArcadePlayer $player) : ?GameSession;

	/**
	 * @return list<GameSession>
	 */
	public function findByGameId(string $gameId) : array;

	/**
	 * @return list<GameSession>
	 */
	public function findByState(GameState $state) : array;

	/**
	 * @return array<string, GameSession>
	 */
	public function all() : array;
}
