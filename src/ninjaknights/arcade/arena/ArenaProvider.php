<?php

declare(strict_types=1);

namespace ninjaknights\arcade\arena;

interface ArenaProvider {

    /**
     * Returns an available arena, or null if none are free.
     */
    public function getAvailableArena() : ?Arena;

    /**
     * Marks an arena as in-use so it won't be allocated again.
     */
    public function claim(Arena $arena) : void;

    /**
     * Releases an arena back to the pool.
     */
    public function release(Arena $arena) : void;
}