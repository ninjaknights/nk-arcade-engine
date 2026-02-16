<?php

declare(strict_types=1);

namespace ninjaknights\arcade\gamesession\policy;

use ninjaknights\arcade\gamesession\GameSession;
use ninjaknights\arcade\gamesession\GameState;
use ninjaknights\arcade\player\ArcadePlayer;
use function in_array;

final class DefaultSessionLifecyclePolicy implements SessionLifecyclePolicyInterface {

	public function __construct(
		private readonly int $minPlayersToStart = 1,
		private readonly bool $autoCloseWhenEmpty = true,
		private readonly ?int $idleTimeoutSeconds = null
	) {}

	public function canJoin(GameSession $gameSession, ArcadePlayer $player) : bool{
		return $gameSession->getState() !== GameState::ENDING
			&& $gameSession->getState() !== GameState::CLOSED;
	}

	public function canTransition(GameSession $gameSession, GameState $from, GameState $to) : bool{
		return match($from){
			GameState::WAITING => in_array($to, [GameState::STARTING, GameState::CLOSED], true),
			GameState::STARTING => in_array($to, [GameState::WAITING, GameState::IN_PROGRESS, GameState::ENDING, GameState::CLOSED], true),
			GameState::IN_PROGRESS => in_array($to, [GameState::ENDING, GameState::CLOSED], true),
			GameState::ENDING => $to === GameState::CLOSED,
			GameState::CLOSED => false,
		};
	}

	public function canStart(GameSession $gameSession) : bool{
		return $gameSession->getPlayerCount() >= $this->minPlayersToStart;
	}

	public function shouldAutoCloseWhenEmpty(GameSession $gameSession) : bool{
		return $this->autoCloseWhenEmpty
			&& $gameSession->getPlayerCount() === 0
			&& $gameSession->getState() !== GameState::CLOSED;
	}

	public function shouldCloseForIdle(GameSession $gameSession, int $currentUnixTime) : bool{
		if($this->idleTimeoutSeconds === null){
			return false;
		}

		if($gameSession->getState() === GameState::CLOSED){
			return false;
		}

		return ($currentUnixTime - $gameSession->getLastActivityAt()) >= $this->idleTimeoutSeconds;
	}
}
