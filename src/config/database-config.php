<?php

use Velsym\Database\DatabaseConfig;
use Velsym\Database\DatabaseDriver;
use Velsym\Database\DatabaseManager;

$config = (new DatabaseConfig())
    ->setDriver(DatabaseDriver::MYSQL)
    ->setHostname("localhost")
    ->setUsername("root")
    ->setPassword("password")
    ->setDatabase("velsym")
    ->setPort(3306)
;
DatabaseManager::setConfig($config);