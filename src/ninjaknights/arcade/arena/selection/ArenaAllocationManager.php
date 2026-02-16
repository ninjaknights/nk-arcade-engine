<?php

declare(strict_types=1);

namespace ninjaknights\arcade\arena\selection;

use ninjaknights\arcade\arena\Arena;
use ninjaknights\arcade\arena\ArenaPoolProviderInterface;

final class ArenaAllocationManager {

	/** @var array<string, int> */
	private array $usageByArenaName = [];

	public function __construct(
		private readonly ArenaPoolProviderInterface $arenaProvider,
		private ArenaSelectionStrategyInterface $selectionStrategy = new RandomArenaSelectionStrategy()
	) {}

	public function setSelectionStrategy(ArenaSelectionStrategyInterface $selectionStrategy) : void{
		$this->selectionStrategy = $selectionStrategy;
	}

	public function getSelectionStrategy() : ArenaSelectionStrategyInterface{
		return $this->selectionStrategy;
	}

	public function allocate() : ?Arena{
		$availableArenas = $this->arenaProvider->getAvailableArenas();
		$arena = $this->selectionStrategy->select($availableArenas, $this->usageByArenaName);
		if($arena === null){
			return null;
		}

		$this->arenaProvider->claim($arena);
		$arenaName = $arena->getName();
		$this->usageByArenaName[$arenaName] = ($this->usageByArenaName[$arenaName] ?? 0) + 1;
		return $arena;
	}

	public function release(Arena $arena) : void{
		$this->arenaProvider->release($arena);
	}

	public function getUsage(string $arenaName) : int{
		return $this->usageByArenaName[$arenaName] ?? 0;
	}

	/**
	 * @return array<string, int>
	 */
	public function getUsageMap() : array{
		return $this->usageByArenaName;
	}

	public function resetUsage() : void{
		$this->usageByArenaName = [];
	}
}
