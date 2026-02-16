<?php

declare(strict_types=1);

namespace ninjaknights\arcade\event\dispatcher;

use pocketmine\event\Event;

interface EventDispatcherInterface {

	public function dispatch(Event $event) : void;
}
