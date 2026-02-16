<?php

declare(strict_types=1);

namespace ninjaknights\arcade;

use ninjaknights\arcade\event\dispatcher\EventDispatcherInterface;
use ninjaknights\arcade\game\Game;
use ninjaknights\arcade\game\GameRegistry;
use ninjaknights\arcade\gamesession\GameSession;
use ninjaknights\arcade\gamesession\SessionLocator;
use ninjaknights\arcade\gamesession\SessionLocatorInterface;
use ninjaknights\arcade\module\FeatureModuleRegistry;
use ninjaknights\arcade\module\FeatureModuleRegistryInterface;
use ninjaknights\arcade\player\PlayerSessionManager;
use ninjaknights\arcade\rejoin\RejoinManager;
use ninjaknights\arcade\rejoin\RejoinManagerInterface;

final class ArcadeEngine {

	/** @var array<string, GameSession> */
	private array $activeSessions = [];

	private readonly SessionLocatorInterface $sessionLocator;
	private readonly RejoinManagerInterface $rejoinManager;
	private readonly FeatureModuleRegistryInterface $featureModules;

	/**
	 * ArcadeEngine constructor.
	 * @param EventDispatcherInterface            $eventDispatcher
	 * @param GameRegistry                        $gameRegistry
	 * @param PlayerSessionManager                $playerSessions
	 * @param SessionLocatorInterface|null        $sessionLocator
	 * @param RejoinManagerInterface|null         $rejoinManager
	 * @param FeatureModuleRegistryInterface|null $featureModules
	 */
	public function __construct(
		private readonly EventDispatcherInterface $eventDispatcher,
		private readonly GameRegistry $gameRegistry = new GameRegistry(),
		private readonly PlayerSessionManager $playerSessions = new PlayerSessionManager(),
		?SessionLocatorInterface $sessionLocator = null,
		?RejoinManagerInterface $rejoinManager = null,
		?FeatureModuleRegistryInterface $featureModules = null
	) {
		$this->sessionLocator = $sessionLocator ?? new SessionLocator(fn() : array => $this->activeSessions);
		$this->rejoinManager = $rejoinManager ?? new RejoinManager($this->eventDispatcher, $this->sessionLocator);
		$this->featureModules = $featureModules ?? new FeatureModuleRegistry();
	}

	public function getGameRegistry() : GameRegistry{
		return $this->gameRegistry;
	}

	public function getEventDispatcher() : EventDispatcherInterface{
		return $this->eventDispatcher;
	}

	public function getPlayerSessions() : PlayerSessionManager{
		return $this->playerSessions;
	}

	public function getSessionLocator() : SessionLocatorInterface{
		return $this->sessionLocator;
	}

	public function getRejoinManager() : RejoinManagerInterface{
		return $this->rejoinManager;
	}

	public function getFeatureModules() : FeatureModuleRegistryInterface{
		return $this->featureModules;
	}

	/**
	 * Creates a new game session from the given game implementation.
	 * The session is tracked by the engine and can be retrieved by ID.
	 */
	public function createGameSession(Game $game) : GameSession{
		$session = $game->createGameSession($this->eventDispatcher);
		$this->activeSessions[$session->getId()] = $session;
		return $session;
	}

	public function getSession(string $sessionId) : ?GameSession{
		return $this->activeSessions[$sessionId] ?? null;
	}

	/**
	 * @return array<string, GameSession>
	 */
	public function getActiveSessions() : array{
		return $this->activeSessions;
	}

	/**
	 * Removes a session from the active tracking list.
	 * Call this after a session has been closed.
	 */
	public function removeSession(string $sessionId) : void{
		unset($this->activeSessions[$sessionId]);
		$this->rejoinManager->clearSession($sessionId);
	}
}
