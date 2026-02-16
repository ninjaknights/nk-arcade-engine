<?php

declare(strict_types=1);

namespace ninjaknights\arcade\gamesession;

use function strtolower;

enum EndReason : string {

	case NORMAL = 'normal';
	case MANUAL = 'manual';
	case EMPTY = 'empty';
	case IDLE_TIMEOUT = 'idle_timeout';
	case PLAYER_DEATH = 'player_death';
	case SYSTEM = 'system';
	case CUSTOM = 'custom';

	public static function fromRaw(string $rawReason) : self{
		return self::tryFrom(strtolower($rawReason)) ?? self::CUSTOM;
	}
}
