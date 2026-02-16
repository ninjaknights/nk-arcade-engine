<?php

declare(strict_types=1);

namespace ninjaknights\arcade\team;

use ninjaknights\arcade\player\ArcadePlayer;

final class Team {

    /**
     * @var array<string, ArcadePlayer>
     */
    private array $members = [];

    public function __construct(
        private readonly string $id,
        private readonly string $displayName
    ) {}

    public function getId() : string{
        return $this->id;
    }

    public function getDisplayName() : string{
        return $this->displayName;
    }

    public function addMember(ArcadePlayer $player) : void{
        $this->members[$player->getUniqueId()] = $player;
    }

    public function removeMember(ArcadePlayer $player) : void{
        unset($this->members[$player->getUniqueId()]);
    }

    /**
     * @return array<string, ArcadePlayer>
     */
    public function getMembers() : array{
        return $this->members;
    }

    public function size() : int{
        return count($this->members);
    }
}