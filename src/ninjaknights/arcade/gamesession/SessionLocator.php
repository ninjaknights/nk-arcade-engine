<?php

declare(strict_types=1);

namespace ninjaknights\arcade\gamesession;

use ninjaknights\arcade\player\ArcadePlayer;

final class SessionLocator implements SessionLocatorInterface {

    /**
     * @param \Closure() : array<string, GameSession> $sessionsProvider
     */
    public function __construct(private readonly \Closure $sessionsProvider) {}

    public function findById(string $sessionId) : ?GameSession{
        return $this->all()[$sessionId] ?? null;
    }

    public function findByPlayer(ArcadePlayer $player) : ?GameSession{
        foreach($this->all() as $session){
            if($session->hasPlayer($player)){
                return $session;
            }
        }

        return null;
    }

    public function findByGameId(string $gameId) : array{
        $sessions = [];

        foreach($this->all() as $session){
            if($session->getGame()->getId() === $gameId){
                $sessions[] = $session;
            }
        }

        return $sessions;
    }

    public function findByState(GameState $state) : array{
        $sessions = [];

        foreach($this->all() as $session){
            if($session->getState() === $state){
                $sessions[] = $session;
            }
        }

        return $sessions;
    }

    public function all() : array{
        $sessions = ($this->sessionsProvider)();
        return $sessions;
    }
}