<?php

declare(strict_types=1);

namespace ninjaknights\arcade\arena\selection;

use ninjaknights\arcade\arena\Arena;

interface ArenaSelectionStrategyInterface {

    /**
     * @param list<Arena> $availableArenas
     * @param array<string, int> $usageCountByArenaName
     */
    public function select(array $availableArenas, array $usageCountByArenaName = []) : ?Arena;
}