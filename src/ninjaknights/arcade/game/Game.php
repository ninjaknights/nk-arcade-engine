<?php

declare(strict_types=1);

namespace ninjaknights\arcade\game;

use ninjaknights\arcade\event\dispatcher\EventDispatcherInterface;
use ninjaknights\arcade\gamesession\GameSession;

interface Game {

	public function getId() : string;

	public function getInfo() : GameInfo;

	/**
	 * Creates a new game session for this game type.
	 * The EventDispatcherInterface is provided by the ArcadeEngine
	 * so game implementations don't need to manage it themselves.
	 */
	public function createGameSession(EventDispatcherInterface $eventDispatcher) : GameSession;
}
