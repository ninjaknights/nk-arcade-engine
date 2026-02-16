<?php

declare(strict_types=1);

namespace ninjaknights\arcade\game;

use ninjaknights\arcade\game\exception\DuplicateGameIdException;
use ninjaknights\arcade\game\exception\GameNotFoundException;

final class GameRegistry implements GameRegistryInterface {

	/** @var array<string, Game> */
	private array $games = [];

	public function register(Game $game) : void{
		$id = $game->getId();
		if($this->has($id)){
			throw new DuplicateGameIdException("Game with id '{$id}' is already registered");
		}

		$this->games[$id] = $game;
	}

	public function unregister(string $id) : void{
		unset($this->games[$id]);
	}

	public function has(string $id) : bool{
		return isset($this->games[$id]);
	}

	public function get(string $id) : Game{
		if(!$this->has($id)){
			throw new GameNotFoundException("Game with id '{$id}' is not registered");
		}

		return $this->games[$id];
	}

	public function all() : array{
		return $this->games;
	}
}
