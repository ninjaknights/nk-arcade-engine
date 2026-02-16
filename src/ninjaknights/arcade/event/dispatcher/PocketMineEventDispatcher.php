<?php

declare(strict_types=1);

namespace ninjaknights\arcade\event\dispatcher;

use pocketmine\event\Event;

final class PocketMineEventDispatcher implements EventDispatcherInterface {

    public function __construct() {}

    public function dispatch(Event $event) : void{
        $event->call();
    }
}