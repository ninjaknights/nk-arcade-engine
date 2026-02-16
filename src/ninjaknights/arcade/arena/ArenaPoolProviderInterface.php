<?php

declare(strict_types=1);

namespace ninjaknights\arcade\arena;

interface ArenaPoolProviderInterface extends ArenaProvider {

    /**
     * @return list<Arena>
     */
    public function getAvailableArenas() : array;
}