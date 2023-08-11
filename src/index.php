<?php

use Velsym\DependencyInjection\DependencyManager;

require "vendor/autoload.php";

DependencyManager::loadDependencies(...(require "dependencies.php"));
