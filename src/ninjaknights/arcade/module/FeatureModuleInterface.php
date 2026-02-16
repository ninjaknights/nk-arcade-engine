<?php

declare(strict_types=1);

namespace ninjaknights\arcade\module;

use ninjaknights\arcade\ArcadeEngine;

interface FeatureModuleInterface {

    public function getId() : string;

    public function onRegistered(ArcadeEngine $engine) : void;

    public function onUnregistered(ArcadeEngine $engine) : void;
}