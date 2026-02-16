# NinjaKnights Arcade SDK - API Documentation

## Overview
NinjaKnights Arcade SDK is a PocketMine-MP virion for building minigames with a reusable, event-driven core.

Core goals:
- Virion-first (no plugin lifecycle ownership)
- Strongly typed, interface-first design
- Session-based game lifecycle
- Optional feature modules (rejoin, teams, arena allocation, etc.)

## Requirements
- PHP 8.1+
- PocketMine-MP API 5.x

## Core Entry Point

### ArcadeEngine
Namespace: `ninjaknights\arcade`

`ArcadeEngine` composes and exposes all core services:
- `GameRegistry`
- `PlayerSessionManager`
- `SessionLocatorInterface`
- `RejoinManagerInterface`
- `FeatureModuleRegistryInterface`

Key methods:
- `createGameSession(Game $game): GameSession`
- `getSession(string $sessionId): ?GameSession`
- `getActiveSessions(): array<string, GameSession>`
- `removeSession(string $sessionId): void`

Service accessors:
- `getGameRegistry()`
- `getEventDispatcher()`
- `getPlayerSessions()`
- `getSessionLocator()`
- `getRejoinManager()`
- `getFeatureModules()`

---

## Game Registration API

### Game
Namespace: `ninjaknights\arcade\game`

```php
interface Game {
    public function getId() : string;
    public function getInfo() : GameInfo;
    public function createGameSession(EventDispatcherInterface $eventDispatcher) : GameSession;
}
```

### GameInfo
Carries metadata:
- name
- description
- min players
- max players

### GameRegistry
Responsibilities:
- register/unregister game types
- prevent duplicate IDs
- lookup by game ID
- list all registered games

---

## Game Session API

### GameSession
Namespace: `ninjaknights\arcade\gamesession`

Responsibilities:
- track session players
- maintain session state
- dispatch lifecycle events
- enforce lifecycle policy hooks

Important methods:
- `addPlayer(ArcadePlayer $player): bool`
- `removePlayer(ArcadePlayer $player): bool`
- `setState(GameState $newState): void`
- `start(): void`
- `end(string $reason = 'normal'): void`
- `close(): void`
- `evaluateLifecycle(int $currentUnixTime = -1): void`

State model (`GameState` enum):
- `WAITING`
- `STARTING`
- `IN_PROGRESS`
- `ENDING`
- `CLOSED`

### Lifecycle Policy Hooks
Namespace: `ninjaknights\arcade\gamesession\policy`

```php
interface SessionLifecyclePolicyInterface {
    public function canJoin(GameSession $gameSession, ArcadePlayer $player) : bool;
    public function canTransition(GameSession $gameSession, GameState $from, GameState $to) : bool;
    public function canStart(GameSession $gameSession) : bool;
    public function shouldAutoCloseWhenEmpty(GameSession $gameSession) : bool;
    public function shouldCloseForIdle(GameSession $gameSession, int $currentUnixTime) : bool;
}
```

Default implementation:
- `DefaultSessionLifecyclePolicy`
- supports minimum players to start
- auto-close when empty
- idle timeout support

---

## Session Query API

### SessionLocatorInterface
Namespace: `ninjaknights\arcade\gamesession`

```php
interface SessionLocatorInterface {
    public function findById(string $sessionId) : ?GameSession;
    public function findByPlayer(ArcadePlayer $player) : ?GameSession;
    public function findByGameId(string $gameId) : array; // list<GameSession>
    public function findByState(GameState $state) : array; // list<GameSession>
    public function all() : array; // array<string, GameSession>
}
```

Default implementation:
- `SessionLocator`

---

## Player API

### ArcadePlayer
Wrapper around PocketMine `Player` with:
- `getPlayer()`
- `getName()`
- `getUniqueId()`
- `getCurrentGameSession()` / `setCurrentGameSession()`
- metadata methods: `setMeta`, `getMeta`, `hasMeta`, `clearMeta`

### PlayerSessionManager
Responsibilities:
- get/create `ArcadePlayer` wrapper from `Player`
- lookup by UUID
- remove tracked wrappers
- list all wrappers

---

## Rejoin API (Optional)

### RejoinManagerInterface
Namespace: `ninjaknights\arcade\rejoin`

```php
interface RejoinManagerInterface {
    public function markRecoverable(ArcadePlayer $player, GameSession $gameSession) : void;
    public function clearRecoverable(string $playerUuid) : void;
    public function canRejoin(ArcadePlayer $player) : bool;
    public function getRecoverableSessionId(string $playerUuid) : ?string;
    public function attemptRejoin(ArcadePlayer $player) : bool;
    public function clearSession(string $sessionId) : void;
}
```

Default implementation:
- `RejoinManager` (in-memory)

---

## Arena API

### Arena
Namespace: `ninjaknights\arcade\arena`

```php
interface Arena {
    public function getName() : string;
    public function getWorldName() : string;
    public function getSpawnPoints() : array; // list<Position>
}
```

### ArenaProvider
Base contract:
- `getAvailableArena(): ?Arena`
- `claim(Arena $arena): void`
- `release(Arena $arena): void`

### ArenaPoolProviderInterface
Extended contract for strategy selection:
- `getAvailableArenas(): array` (list of arenas)

### Arena Allocation Strategies
Namespace: `ninjaknights\arcade\arena\selection`

- `ArenaSelectionStrategyInterface`
- `RandomArenaSelectionStrategy`
- `LeastUsedArenaSelectionStrategy`
- `ArenaAllocationManager` (tracks usage counts and claims/releases via provider)

---

## Team API (Optional)
Namespace: `ninjaknights\arcade\team`

Includes:
- `Team`
- `TeamManager`
- `TeamBalancer`

Use this module only when game modes require teams.

---

## Utility API
Namespace: `ninjaknights\arcade\utility`

Includes:
- `CountdownTimer`
- `GameSessionBroadcaster`
- `SafeTeleporter`
- `ScoreboardAdapter` (`utility\scoreboard`)

---

## Feature Module Registry API
Namespace: `ninjaknights\arcade\module`

### FeatureModuleInterface
```php
interface FeatureModuleInterface {
    public function getId() : string;
    public function onRegistered(ArcadeEngine $engine) : void;
    public function onUnregistered(ArcadeEngine $engine) : void;
}
```

### FeatureModuleRegistryInterface
```php
interface FeatureModuleRegistryInterface {
    public function register(ArcadeEngine $engine, FeatureModuleInterface $module) : void;
    public function unregister(ArcadeEngine $engine, string $moduleId) : void;
    public function has(string $moduleId) : bool;
    public function get(string $moduleId) : FeatureModuleInterface;
    public function all() : array; // array<string, FeatureModuleInterface>
}
```

Default implementation:
- `FeatureModuleRegistry`

Exceptions:
- `DuplicateFeatureModuleIdException`
- `FeatureModuleNotFoundException`

---

## Event Reference

### Game Session Events
Namespace: `ninjaknights\arcade\event\gamesession`
- `GameSessionStartEvent`
- `GameSessionEndEvent`
- `GameSessionStateChangeEvent`
- `PlayerJoinGameSessionEvent` (cancellable)
- `PlayerLeaveGameSessionEvent` (cancellable)

### Rejoin Events
Namespace: `ninjaknights\arcade\event\rejoin`
- `PlayerRejoinAttemptEvent` (cancellable)
- `PlayerRejoinSuccessEvent`
- `PlayerRejoinFailedEvent`

---

## Typical Integration Flow

1. Construct `ArcadeEngine` with an `EventDispatcherInterface` implementation.
2. Register game implementations in `GameRegistry`.
3. Create sessions using `ArcadeEngine::createGameSession()`.
4. Join/leave players via `GameSession` methods.
5. Periodically call `GameSession::evaluateLifecycle()` if using idle timeout policy.
6. Use `SessionLocator` for orchestration queries.
7. Optionally use `RejoinManager` on disconnect/reconnect flows.
8. Remove closed sessions using `ArcadeEngine::removeSession()`.

---

## Practical Example: Register and Start a Session

```php
$engine = new ArcadeEngine(new PocketMineEventDispatcher());
$engine->getGameRegistry()->register(new SpleefGame());

$game = $engine->getGameRegistry()->get('spleef');
$session = $engine->createGameSession($game);

$arcadePlayer = $engine->getPlayerSessions()->get($player);
$session->addPlayer($arcadePlayer);
$session->start();
```

## Practical Example: Rejoin Flow

```php
// On disconnect
$rejoin = $engine->getRejoinManager();
$rejoin->markRecoverable($arcadePlayer, $session);

// On reconnect
$rejoin->attemptRejoin($arcadePlayer);
```

## Practical Example: Feature Module Registration

```php
$engine->getFeatureModules()->register($engine, new class implements FeatureModuleInterface {
    public function getId() : string { return 'example.module'; }
    public function onRegistered(ArcadeEngine $engine) : void {}
    public function onUnregistered(ArcadeEngine $engine) : void {}
});
```

---

## Notes
- This virion intentionally excludes persistence engines, economy, cosmetics, command frameworks, and HTTP logic from core.
- Build those concerns as separate optional adapters/packages around this API.
