<?php

declare(strict_types=1);

namespace ninjaknights\arcade\module;

use ninjaknights\arcade\ArcadeEngine;

interface FeatureModuleRegistryInterface {

	public function register(ArcadeEngine $engine, FeatureModuleInterface $module) : void;

	public function unregister(ArcadeEngine $engine, string $moduleId) : void;

	public function has(string $moduleId) : bool;

	public function get(string $moduleId) : FeatureModuleInterface;

	/**
	 * @return array<string, FeatureModuleInterface>
	 */
	public function all() : array;
}
