<?php

declare(strict_types=1);

namespace ninjaknights\arcade\arena;

use pocketmine\world\Position;

interface Arena {

	public function getName() : string;

	/**
	 * @return list<Position>
	 */
	public function getSpawnPoints() : array;

	/**
	 * Returns the world name this arena belongs to.
	 */
	public function getWorldName() : string;
}
