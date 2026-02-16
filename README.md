# NinjaKnights Arcade SDK (Virion)

Reusable, event-driven arcade framework for PocketMine-MP game developers.

## Documentation

- Full API docs: [API_DOCUMENTATION.md](API_DOCUMENTATION.md)
- Architecture guide: [ARCADE_SDK_ARCHITECTURE.md](ARCADE_SDK_ARCHITECTURE.md)

## Goals

- Virion only (no plugin lifecycle methods)
- No monetization, Tebex, or HTTP logic
- Interface-first and dependency-injected architecture
- Game session lifecycle with custom events
- Extensible for third-party game implementations

## Included Modules

- `Game` module: `Game`, `GameInfo`, `GameRegistry`
- `GameSession` module: `GameSession`, `GameState`
- `Lifecycle Policy` module: `SessionLifecyclePolicyInterface`, `DefaultSessionLifecyclePolicy`
- `SessionLocator` module: `SessionLocatorInterface`, `SessionLocator`
- `Feature Modules` module: `FeatureModuleInterface`, `FeatureModuleRegistryInterface`, `FeatureModuleRegistry`
- `Player` module: `ArcadePlayer`, `PlayerSessionManager`
- `Rejoin` module (optional): `RejoinManagerInterface`, `RejoinManager`
- `Arena` module: `Arena`, `ArenaProvider`
- `Arena Selection` module: `ArenaPoolProviderInterface`, `ArenaSelectionStrategyInterface`, `ArenaAllocationManager`, `RandomArenaSelectionStrategy`, `LeastUsedArenaSelectionStrategy`
- `Team` module (optional): `Team`, `TeamManager`, `TeamBalancer`
- `Utility` module: `CountdownTimer`, `GameSessionBroadcaster`, `SafeTeleporter`, `ScoreboardAdapter`
- Events: `GameSessionStartEvent`, `GameSessionEndEvent`, `PlayerJoinGameSessionEvent`, `PlayerLeaveGameSessionEvent`, `GameSessionStateChangeEvent`, `PlayerRejoinAttemptEvent`, `PlayerRejoinSuccessEvent`, `PlayerRejoinFailedEvent`

## Installation (Virion)

Add as a virion dependency through Poggit/Composer in your plugin project.

## Quick Start

```php
<?php

declare(strict_types=1);

namespace YourPlugin;

use ninjaknights\arcade\ArcadeEngine;
use ninjaknights\arcade\event\dispatcher\EventDispatcherInterface;
use ninjaknights\arcade\event\dispatcher\PocketMineEventDispatcher;
use ninjaknights\arcade\game\Game;
use ninjaknights\arcade\game\GameInfo;
use ninjaknights\arcade\gamesession\GameSession;
use pocketmine\plugin\PluginBase;

final class Main extends PluginBase {

	private ArcadeEngine $arcade;

	protected function onEnable() : void{
		$this->arcade = new ArcadeEngine(
			new PocketMineEventDispatcher()
		);

		$this->arcade->getGameRegistry()->register(
			new SpleefGame($this->arcade)
		);
	}
}

final class SpleefGame implements Game {

	public function __construct(private readonly ArcadeEngine $arcade) {}

	public function getId() : string{
		return 'spleef';
	}

	public function getInfo() : GameInfo{
		return new GameInfo('Spleef', 'Break blocks under your opponents', 2, 16);
	}

	public function createGameSession(EventDispatcherInterface $dispatcher) : GameSession{
		return new GameSession(
			uniqid('spleef_', true),
			$this,
			$dispatcher
		);
	}
}
```

## Feature Direction (based on MinigameAPI)

### Add to core (next)
- (none currently; core parity targets completed)

### Keep out of core (optional packages)
- YAML/SQL/Mongo persistence implementations
- Stats/leaderboards implementation
- Command framework integration

### Not planned in core
- Economy/coins
- Cosmetics

## Design Notes

- `GameSession` contains lifecycle and player tracking only.
- Game-specific rules stay in game implementations/listeners.
- Event listeners can cancel join/leave flows.
- Arena loading/world management is intentionally outside this SDK.