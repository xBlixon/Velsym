<?php

use Velsym\DependencyInjection\DependencyManager;
use Velsym\Routing\Router;

require __DIR__ . '/../vendor/autoload.php';
require "config/config.php";

DependencyManager::loadDependencies(array_merge(...(require "config/dependencies.php")));
$routes = __DIR__ . "/routes";
$router = new Router($routes, "App\\Routes\\");
$router->handle();