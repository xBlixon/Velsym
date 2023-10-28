<?php

use Velsym\Auth\Roles;

require "database-config.php";

$roles = [
    "USER" => 10,
    "PREMIUM" => 20,
    "ADMIN" => 100
];
Roles::set($roles);


