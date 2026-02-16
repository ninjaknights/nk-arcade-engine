<?php

declare(strict_types=1);

namespace ninjaknights\arcade\gamesession;

final class SessionResult {

	/**
	 * @param list<string> $winnerUuids
	 */
	public function __construct(
		private readonly EndReason $reason,
		private readonly string $rawReason,
		private readonly int $startedAt,
		private readonly int $endedAt,
		private readonly int $playerCount,
		private readonly array $winnerUuids = []
	) {}

	public function getReason() : EndReason{
		return $this->reason;
	}

	public function getRawReason() : string{
		return $this->rawReason;
	}

	public function getStartedAt() : int{
		return $this->startedAt;
	}

	public function getEndedAt() : int{
		return $this->endedAt;
	}

	public function getDurationSeconds() : int{
		return $this->endedAt - $this->startedAt;
	}

	public function getPlayerCount() : int{
		return $this->playerCount;
	}

	/**
	 * @return list<string>
	 */
	public function getWinnerUuids() : array{
		return $this->winnerUuids;
	}
}
