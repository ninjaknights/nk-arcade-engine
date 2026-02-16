<?php

declare(strict_types=1);

namespace ninjaknights\arcade\gamesession;

use ninjaknights\arcade\event\dispatcher\EventDispatcherInterface;
use ninjaknights\arcade\event\gamesession\GameSessionEndEvent;
use ninjaknights\arcade\event\gamesession\GameSessionStartEvent;
use ninjaknights\arcade\event\gamesession\GameSessionStateChangeEvent;
use ninjaknights\arcade\event\gamesession\PlayerJoinGameSessionEvent;
use ninjaknights\arcade\event\gamesession\PlayerLeaveGameSessionEvent;
use ninjaknights\arcade\game\Game;
use ninjaknights\arcade\gamesession\policy\DefaultSessionLifecyclePolicy;
use ninjaknights\arcade\gamesession\policy\SessionLifecyclePolicyInterface;
use ninjaknights\arcade\player\ArcadePlayer;

class GameSession {

    /**
     * @var array<string, ArcadePlayer>
     */
    private array $players = [];

    private GameState $state = GameState::WAITING;
    private int $lastActivityAt;
    private readonly SessionLifecyclePolicyInterface $lifecyclePolicy;

    public function __construct(
        private readonly string $id,
        private readonly Game $game,
        private readonly EventDispatcherInterface $eventDispatcher,
        ?SessionLifecyclePolicyInterface $lifecyclePolicy = null
    ) {
        $this->lastActivityAt = time();
        $this->lifecyclePolicy = $lifecyclePolicy ?? new DefaultSessionLifecyclePolicy();
    }

    public function getId() : string{
        return $this->id;
    }

    public function getGame() : Game{
        return $this->game;
    }

    public function getState() : GameState{
        return $this->state;
    }

    public function getLifecyclePolicy() : SessionLifecyclePolicyInterface{
        return $this->lifecyclePolicy;
    }

    public function getLastActivityAt() : int{
        return $this->lastActivityAt;
    }

    public function hasPlayer(ArcadePlayer $arcadePlayer) : bool{
        return isset($this->players[$arcadePlayer->getUniqueId()]);
    }

    public function addPlayer(ArcadePlayer $arcadePlayer) : bool{
        if($this->state === GameState::ENDING || $this->state === GameState::CLOSED){
            return false;
        }

        if(!$this->lifecyclePolicy->canJoin($this, $arcadePlayer)){
            return false;
        }

        if($this->hasPlayer($arcadePlayer)){
            return false;
        }

        $joinEvent = new PlayerJoinGameSessionEvent($this, $arcadePlayer);
        $this->eventDispatcher->dispatch($joinEvent);
        if($joinEvent->isCancelled()){
            return false;
        }

        $key = $arcadePlayer->getUniqueId();
        $this->players[$key] = $arcadePlayer;
        $arcadePlayer->setCurrentGameSession($this);

        $this->touchActivity();

        if($this->state === GameState::WAITING && $this->lifecyclePolicy->canStart($this)){
            $this->setState(GameState::STARTING);
        }

        return true;
    }

    public function removePlayer(ArcadePlayer $arcadePlayer) : bool{
        $key = $arcadePlayer->getUniqueId();
        if(!isset($this->players[$key])){
            return false;
        }

        $leaveEvent = new PlayerLeaveGameSessionEvent($this, $arcadePlayer);
        $this->eventDispatcher->dispatch($leaveEvent);
        if($leaveEvent->isCancelled()){
            return false;
        }

        unset($this->players[$key]);
        $arcadePlayer->setCurrentGameSession(null);

        $this->touchActivity();

        if($this->lifecyclePolicy->shouldAutoCloseWhenEmpty($this)){
            $this->end('empty');
        }

        return true;
    }

    /**
     * @return array<string, ArcadePlayer>
     */
    public function getPlayers() : array{
        return $this->players;
    }

    public function getPlayerCount() : int{
        return count($this->players);
    }

    public function setState(GameState $newState) : void{
        if($this->state === $newState){
            return;
        }

        $oldState = $this->state;
        if(!$this->lifecyclePolicy->canTransition($this, $oldState, $newState)){
            return;
        }

        $this->state = $newState;
        $this->touchActivity();
        $this->eventDispatcher->dispatch(new GameSessionStateChangeEvent($this, $oldState, $newState));

        if($newState === GameState::IN_PROGRESS){
            $this->eventDispatcher->dispatch(new GameSessionStartEvent($this));
        }
    }

    public function start() : void{
        if(!$this->lifecyclePolicy->canStart($this)){
            return;
        }

        if($this->state === GameState::WAITING){
            $this->setState(GameState::STARTING);
        }

        $this->setState(GameState::IN_PROGRESS);
    }

    public function end(string $reason = 'normal') : void{
        if($this->state === GameState::CLOSED){
            return;
        }

        $this->setState(GameState::ENDING);
        $this->eventDispatcher->dispatch(new GameSessionEndEvent($this, $reason));
        $this->cleanupPlayers();
        $this->setState(GameState::CLOSED);
    }

    public function close() : void{
        $this->cleanupPlayers();
        $this->setState(GameState::CLOSED);
    }

    public function evaluateLifecycle(int $currentUnixTime = -1) : void{
        $now = $currentUnixTime === -1 ? time() : $currentUnixTime;

        if($this->lifecyclePolicy->shouldCloseForIdle($this, $now)){
            $this->end('idle_timeout');
        }
    }

    /**
     * Removes all player references when the session ends.
     */
    private function cleanupPlayers() : void{
        foreach($this->players as $player){
            $player->setCurrentGameSession(null);
        }
        $this->players = [];
    }

    private function touchActivity() : void{
        $this->lastActivityAt = time();
    }
}