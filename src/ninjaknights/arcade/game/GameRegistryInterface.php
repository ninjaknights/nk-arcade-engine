<?php

declare(strict_types=1);

namespace ninjaknights\arcade\game;

interface GameRegistryInterface {

    public function register(Game $game) : void;

    public function unregister(string $id) : void;

    public function has(string $id) : bool;

    public function get(string $id) : Game;

    /**
     * @return array<string, Game>
     */
    public function all() : array;
}