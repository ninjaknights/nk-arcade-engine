<?php

declare(strict_types=1);

namespace ninjaknights\arcade\arena\selection;

use ninjaknights\arcade\arena\Arena;

final class RandomArenaSelectionStrategy implements ArenaSelectionStrategyInterface {

    public function select(array $availableArenas, array $usageCountByArenaName = []) : ?Arena{
        if($availableArenas === []){
            return null;
        }

        return $availableArenas[array_rand($availableArenas)];
    }
}