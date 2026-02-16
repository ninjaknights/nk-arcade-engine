<?php

declare(strict_types=1);

namespace ninjaknights\arcade\game;

final class GameInfo {

    public function __construct(
        private readonly string $name,
        private readonly string $description = "",
        private readonly int $minPlayers = 1,
        private readonly int $maxPlayers = 0
    ) {}

    public function getName() : string{
        return $this->name;
    }

    public function getDescription() : string{
        return $this->description;
    }

    public function getMinPlayers() : int{
        return $this->minPlayers;
    }

    public function getMaxPlayers() : int{
        return $this->maxPlayers;
    }
}