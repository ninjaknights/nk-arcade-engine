<?php

declare(strict_types=1);

namespace ninjaknights\arcade\gamesession;

enum GameState : string {

	case WAITING = 'WAITING';
	case STARTING = 'STARTING';
	case IN_PROGRESS = 'IN_PROGRESS';
	case ENDING = 'ENDING';
	case CLOSED = 'CLOSED';

	/**
	 * @return list<GameState>
	 */
	public static function values() : array{
		return self::cases();
	}

	public static function isValid(string $state) : bool{
		return self::tryFrom($state) !== null;
	}
}
