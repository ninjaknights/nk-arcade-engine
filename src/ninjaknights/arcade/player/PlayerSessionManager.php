<?php

declare(strict_types=1);

namespace ninjaknights\arcade\player;

use pocketmine\player\Player;

final class PlayerSessionManager {

    /**
     * @var array<string, ArcadePlayer>
     */
    private array $sessions = [];

    public function get(Player $player) : ArcadePlayer{
        $id = $player->getUniqueId()->toString();
        return $this->sessions[$id] ??= new ArcadePlayer($player);
    }

    public function findByUuid(string $uuid) : ?ArcadePlayer{
        return $this->sessions[$uuid] ?? null;
    }

    public function remove(Player|string $playerOrUuid) : void{
        $id = $playerOrUuid instanceof Player
            ? $playerOrUuid->getUniqueId()->toString()
            : $playerOrUuid;

        unset($this->sessions[$id]);
    }

    /**
     * @return array<string, ArcadePlayer>
     */
    public function all() : array{
        return $this->sessions;
    }
}