<?php

declare(strict_types=1);

namespace ninjaknights\arcade\utility;

use pocketmine\scheduler\TaskHandler;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\scheduler\ClosureTask;

final class CountdownTimer {

    private bool $running = false;
    private ?TaskHandler $taskHandler = null;
    private int $tickCounter = 0;

    /**
     * @param int $seconds Total countdown duration in seconds
     * @param \Closure(int $secondsLeft): void $onTick Called every second with remaining seconds
     * @param \Closure(): void $onComplete Called when the countdown reaches zero
     */
    public function __construct(
        private int $seconds,
        private readonly \Closure $onTick,
        private readonly \Closure $onComplete
    ) {}

    /**
     * Starts the countdown using a PocketMine scheduler (20 ticks = 1 second).
     * If no scheduler is provided, you must call tick() manually each second.
     */
    public function start(?TaskScheduler $scheduler = null) : void{
        $this->running = true;
        $this->tickCounter = 0;

        if($scheduler !== null){
            $this->taskHandler = $scheduler->scheduleRepeatingTask(new ClosureTask(function() : void{
                $this->tickCounter++;
                if($this->tickCounter >= 20){
                    $this->tickCounter = 0;
                    $this->tick();
                }
            }), 1); // runs every tick, counts to 20 for 1 second
        }
    }

    public function cancel() : void{
        $this->running = false;
        if($this->taskHandler !== null){
            $this->taskHandler->cancel();
            $this->taskHandler = null;
        }
    }

    public function isRunning() : bool{
        return $this->running;
    }

    public function getSecondsLeft() : int{
        return $this->seconds;
    }

    /**
     * Call this once per second if not using the scheduler-based start().
     */
    public function tick() : void{
        if(!$this->running || $this->seconds <= 0){
            return;
        }

        $this->seconds--;
        ($this->onTick)($this->seconds);

        if($this->seconds <= 0){
            $this->cancel();
            ($this->onComplete)();
        }
    }
}