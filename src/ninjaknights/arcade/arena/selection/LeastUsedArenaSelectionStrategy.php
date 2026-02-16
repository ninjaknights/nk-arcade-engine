<?php

declare(strict_types=1);

namespace ninjaknights\arcade\arena\selection;

use ninjaknights\arcade\arena\Arena;
use function array_rand;

final class LeastUsedArenaSelectionStrategy implements ArenaSelectionStrategyInterface {

	public function select(array $availableArenas, array $usageCountByArenaName = []) : ?Arena{
		if($availableArenas === []){
			return null;
		}

		$leastUsage = null;
		$candidates = [];

		foreach($availableArenas as $arena){
			$arenaName = $arena->getName();
			$usage = $usageCountByArenaName[$arenaName] ?? 0;

			if($leastUsage === null || $usage < $leastUsage){
				$leastUsage = $usage;
				$candidates = [$arena];
				continue;
			}

			if($usage === $leastUsage){
				$candidates[] = $arena;
			}
		}

		if($candidates === []){
			return null;
		}

		return $candidates[array_rand($candidates)];
	}
}
