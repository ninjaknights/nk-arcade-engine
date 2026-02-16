# 🥷 NinjaKnights Arcade SDK – Architecture Guide (Rewritten)

## 🎯 Vision
Build a PocketMine-MP virion that gives developers a reusable, event-driven minigame framework.

This SDK is inspired by proven MinigameAPI patterns (manager-first architecture, clear lifecycle, optional modules), but adapted for PocketMine and virion usage.

## ✅ Non-Negotiable Constraints

### 1) Virion only (not a plugin)
- No `onEnable()` / `onDisable()`.
- No direct startup/bootstrap side effects.
- No plugin.yml assumptions.
- No automatic command registration.

### 2) Pure framework core
- No monetization features in core.
- No Tebex integration.
- No HTTP clients in core.
- No persistence engine hardcoded into core.

### 3) Server-agnostic integration
- No dependency on NinjaKnights Core.
- Third-party plugins must be able to consume this virion cleanly.

---

## 🧠 Design Principles

### SRP first
Each class should own one responsibility.

Examples:
- `GameRegistry`: registers game definitions.
- `GameSession`: manages one running session lifecycle.
- `PlayerSessionManager`: maps PocketMine players to `ArcadePlayer` wrappers.

### Event-driven over hardcoded flow
The SDK exposes lifecycle hooks through events so host plugins can override behavior.

Required event family:
- `GameSessionStartEvent`
- `GameSessionEndEvent`
- `PlayerJoinGameSessionEvent`
- `PlayerLeaveGameSessionEvent`
- `GameSessionStateChangeEvent`

### Dependency Injection over static globals
Avoid singletons such as `Arcade::getInstance()`.

Prefer:
- constructor injection
- narrow interfaces
- replaceable implementations

### Interface-first API
Public extension points must be interfaces, not concrete-only APIs.

---

## 🧩 Core Modules

## 1) Engine and Orchestration
`ArcadeEngine` is the composition root used by plugin developers.

Responsibilities:
- Hold references to core managers (`GameRegistry`, `PlayerSessionManager`, dispatcher).
- Create and track active `GameSession` instances.
- Expose retrieval/removal of active sessions.

It does **not** run plugin lifecycle logic.

## 2) Game Registry Module
Purpose: register game types and metadata.

Requirements:
- Register/unregister by game ID.
- Duplicate ID protection.
- Fetch one game or list all games.

Recommended API shape:
```php
interface Game {
    public function getId() : string;
    public function getInfo() : GameInfo;
    public function createGameSession(EventDispatcherInterface $dispatcher) : GameSession;
}
```

## 3) GameSession Module
Purpose: represent one running match/session instance.

Canonical states:
- `WAITING`
- `STARTING`
- `IN_PROGRESS`
- `ENDING`
- `CLOSED`

Rules:
- Track current players.
- Manage controlled state transitions.
- Emit lifecycle events.
- Contain no game-specific win logic.

Game-specific behavior must live in game implementations/listeners, not the session core.

## 4) Player Session Module
Wrap PocketMine `Player` in an SDK model:

```php
final class ArcadePlayer {
    private Player $player;
    private ?GameSession $currentGameSession;
    /** @var array<string, mixed> */
    private array $metadata;
}
```

Why:
- metadata storage
- game/session context tracking
- future-safe expansion without mutating PocketMine internals

## 5) Arena Module (Abstract)
Core should define arena contracts only.

```php
interface Arena {
    public function getName() : string;
    public function getWorldName() : string;
    /** @return list<Position> */
    public function getSpawnPoints() : array;
}

interface ArenaProvider {
    public function getAvailableArena() : ?Arena;
    public function claim(Arena $arena) : void;
    public function release(Arena $arena) : void;
}
```

No world loading, copying, or persistence logic in SDK core.

## 6) Team Module (Optional)
Include only generic team primitives:
- `Team`
- `TeamManager`
- `TeamBalancer`

Module must be optional and independent.

## 7) Utility Module
Reusable helpers only:
- countdown timers
- scoreboard abstraction
- session broadcasting
- safe teleportation helpers

No game-specific text, rewards, or economy assumptions.

---

## 🧱 Manager Pattern (Reference-Aligned)
Following MinigameAPI lessons, manager classes should coordinate collections and lifecycle without containing domain-specific game rules.

Recommended managers:
- `GameRegistry` (game definitions)
- `PlayerSessionManager` (player wrappers)
- `ArenaProvider` implementation in host plugin (arena pool/selection)
- optional `TeamManager` (team assignment)

Managers should be easily swappable by interface.

---

## 🔄 Lifecycle Contract
The expected session lifecycle is:

1. Create session from a registered `Game`.
2. Session enters `WAITING`.
3. Players join/leave with cancellable events.
4. Transition `STARTING` → `IN_PROGRESS`.
5. End transition `ENDING` → `CLOSED`.
6. Cleanup players and release arena.

All transitions should go through one state mutation path (`setState`) to guarantee event consistency.

---

## 🧷 Extension Points
Core extension points must remain open via interfaces:
- Event dispatcher adapter
- Scoreboard adapter
- Arena provider implementation
- Optional module managers

Host plugins can add features like stats, rejoin, cosmetics, persistence, and commands outside the core virion.

---

## 🚫 Out of Scope for Core
These are explicitly not part of SDK core:
- SQL/YAML storage implementations
- economy/coins
- cosmetics
- command framework
- HTTP integrations
- plugin startup wiring

These can exist as separate adapters/modules in consumer plugins.

---

## 📊 MinigameAPI Feature Parity Decisions (Add / Keep / Remove)

Based on MinigameAPI docs + repository feature list, this is the recommended direction for this SDK.

### Add to this API (high value)
- **Rejoin module (optional):** recover player-to-session mapping after disconnect and allow guarded rejoin.
- **Session query API:** search active sessions by player, game ID, and state for easier orchestration.
- **Lifecycle policy hooks:** pluggable start/end rules (min players, auto-close, idle timeout).
- **Arena allocation strategy interface:** weighted/random/least-used arena selection as interchangeable strategies.
- **Feature module registry (lightweight):** optional module discovery without plugin lifecycle coupling.

Status in current SDK:
- Rejoin module: ✅ implemented
- Session query API: ✅ implemented via `SessionLocator`
- Arena allocation strategy: ✅ implemented via `ArenaAllocationManager` + strategy interfaces
- Lifecycle policy hooks: ✅ implemented via `SessionLifecyclePolicyInterface` + `DefaultSessionLifecyclePolicy`
- Feature module registry: ✅ implemented via `FeatureModuleRegistryInterface` + `FeatureModuleRegistry`

### Add as separate packages (NOT core)
- **Persistence adapters:** YAML, SQL, Mongo adapters that implement interfaces from this virion.
- **Stats package:** generic stats interfaces/events; storage implementation outside core.
- **Command helpers package:** optional command abstractions for consumer plugins.

### Keep as-is
- Event-driven `GameSession` lifecycle model.
- Interface-first architecture and DI.
- Optional team module.
- Utility abstractions (timer, broadcasting, teleport, scoreboard adapter).

### Remove / avoid in this API
- Any built-in coins/economy system.
- Any built-in cosmetics system.
- Any server bootstrapping/plugin startup workflow.
- Any hard dependency on persistence format or provider.
- Any game-specific rule engines in core session classes.

### Why this split
MinigameAPI is intentionally broad (stats, coins, cosmetics, commands, DB modules). This virion should stay focused as a reusable runtime core and expose extension points so those concerns can be layered in optional packages.

---

## 🧪 Quality and API Guarantees
- Strong typing (`declare(strict_types=1)`).
- Prefer enums for bounded state.
- No hidden globals.
- Predictable event ordering.
- No hardcoded messages/config paths.
- Backward-compatible public interfaces where possible.

---

## 📌 Implementation Checklist for AI Agents
When generating or modifying SDK code, always verify:

1. Is this virion-safe (no plugin lifecycle logic)?
2. Is this generic (not NinjaKnights-specific)?
3. Does this add an interface for extensibility?
4. Does session lifecycle emit appropriate events?
5. Is any game-specific logic incorrectly inside core?
6. Are optional features kept as optional modules/adapters?

If any answer is no, refactor before finalizing.