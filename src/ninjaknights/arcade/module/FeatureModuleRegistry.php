<?php

declare(strict_types=1);

namespace ninjaknights\arcade\module;

use ninjaknights\arcade\ArcadeEngine;
use ninjaknights\arcade\module\exception\DuplicateFeatureModuleIdException;
use ninjaknights\arcade\module\exception\FeatureModuleNotFoundException;

final class FeatureModuleRegistry implements FeatureModuleRegistryInterface {

	/** @var array<string, FeatureModuleInterface> */
	private array $modules = [];

	public function register(ArcadeEngine $engine, FeatureModuleInterface $module) : void{
		$id = $module->getId();
		if($this->has($id)){
			throw new DuplicateFeatureModuleIdException("Feature module with id '{$id}' is already registered");
		}

		$this->modules[$id] = $module;
		$module->onRegistered($engine);
	}

	public function unregister(ArcadeEngine $engine, string $moduleId) : void{
		$module = $this->modules[$moduleId] ?? null;
		if($module === null){
			throw new FeatureModuleNotFoundException("Feature module with id '{$moduleId}' is not registered");
		}

		unset($this->modules[$moduleId]);
		$module->onUnregistered($engine);
	}

	public function has(string $moduleId) : bool{
		return isset($this->modules[$moduleId]);
	}

	public function get(string $moduleId) : FeatureModuleInterface{
		$module = $this->modules[$moduleId] ?? null;
		if($module === null){
			throw new FeatureModuleNotFoundException("Feature module with id '{$moduleId}' is not registered");
		}

		return $module;
	}

	public function all() : array{
		return $this->modules;
	}
}
