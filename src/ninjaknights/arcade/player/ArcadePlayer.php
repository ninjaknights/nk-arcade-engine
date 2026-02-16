<?php

declare(strict_types=1);

namespace ninjaknights\arcade\player;

use ninjaknights\arcade\gamesession\GameSession;
use pocketmine\player\Player;

final class ArcadePlayer {

    /**
     * @var array<string, mixed>
     */
    private array $metadata = [];

    private ?GameSession $currentGameSession = null;

    public function __construct(private readonly Player $player) {}

    public function getPlayer() : Player{
        return $this->player;
    }

    public function getName() : string{
        return $this->player->getName();
    }

    public function getUniqueId() : string{
        return $this->player->getUniqueId()->toString();
    }

    public function getCurrentGameSession() : ?GameSession{
        return $this->currentGameSession;
    }

    public function setCurrentGameSession(?GameSession $gameSession) : void{
        $this->currentGameSession = $gameSession;
    }

    public function setMeta(string $key, mixed $value) : void{
        $this->metadata[$key] = $value;
    }

    public function hasMeta(string $key) : bool{
        return array_key_exists($key, $this->metadata);
    }

    public function getMeta(string $key, mixed $default = null) : mixed{
        return $this->metadata[$key] ?? $default;
    }

    public function clearMeta(string $key) : void{
        unset($this->metadata[$key]);
    }
}