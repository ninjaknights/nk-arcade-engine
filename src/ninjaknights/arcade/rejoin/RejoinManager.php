<?php

declare(strict_types=1);

namespace ninjaknights\arcade\rejoin;

use ninjaknights\arcade\event\dispatcher\EventDispatcherInterface;
use ninjaknights\arcade\event\rejoin\PlayerRejoinAttemptEvent;
use ninjaknights\arcade\event\rejoin\PlayerRejoinFailedEvent;
use ninjaknights\arcade\event\rejoin\PlayerRejoinSuccessEvent;
use ninjaknights\arcade\gamesession\GameSession;
use ninjaknights\arcade\gamesession\SessionLocatorInterface;
use ninjaknights\arcade\player\ArcadePlayer;

final class RejoinManager implements RejoinManagerInterface {

    /**
     * @var array<string, string>
     */
    private array $recoverableSessions = [];

    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly SessionLocatorInterface $sessionLocator
    ) {}

    public function markRecoverable(ArcadePlayer $player, GameSession $gameSession) : void{
        $this->recoverableSessions[$player->getUniqueId()] = $gameSession->getId();
    }

    public function clearRecoverable(string $playerUuid) : void{
        unset($this->recoverableSessions[$playerUuid]);
    }

    public function canRejoin(ArcadePlayer $player) : bool{
        return isset($this->recoverableSessions[$player->getUniqueId()]);
    }

    public function getRecoverableSessionId(string $playerUuid) : ?string{
        return $this->recoverableSessions[$playerUuid] ?? null;
    }

    public function attemptRejoin(ArcadePlayer $player) : bool{
        $playerUuid = $player->getUniqueId();
        $sessionId = $this->recoverableSessions[$playerUuid] ?? null;

        if($sessionId === null){
            $this->eventDispatcher->dispatch(new PlayerRejoinFailedEvent($player, null, PlayerRejoinFailedEvent::NO_RECOVERABLE_SESSION));
            return false;
        }

        $session = $this->sessionLocator->findById($sessionId);
        if($session === null){
            $this->clearRecoverable($playerUuid);
            $this->eventDispatcher->dispatch(new PlayerRejoinFailedEvent($player, null, PlayerRejoinFailedEvent::SESSION_NOT_FOUND));
            return false;
        }

        $attemptEvent = new PlayerRejoinAttemptEvent($player, $session);
        $this->eventDispatcher->dispatch($attemptEvent);
        if($attemptEvent->isCancelled()){
            $this->eventDispatcher->dispatch(new PlayerRejoinFailedEvent($player, $session, PlayerRejoinFailedEvent::ATTEMPT_CANCELLED));
            return false;
        }

        if(!$session->addPlayer($player)){
            $this->eventDispatcher->dispatch(new PlayerRejoinFailedEvent($player, $session, PlayerRejoinFailedEvent::SESSION_REJECTED_PLAYER));
            return false;
        }

        $this->clearRecoverable($playerUuid);
        $this->eventDispatcher->dispatch(new PlayerRejoinSuccessEvent($player, $session));
        return true;
    }

    public function clearSession(string $sessionId) : void{
        foreach($this->recoverableSessions as $playerUuid => $recoverableSessionId){
            if($recoverableSessionId === $sessionId){
                unset($this->recoverableSessions[$playerUuid]);
            }
        }
    }
}