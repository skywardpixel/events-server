<?php

use Tuupola\Middleware\Cors;

$app->add(new Cors([
    "origin" => ["*"],
    "methods" => ["GET", "POST", "PUT", "PATCH", "DELETE"],
    "headers.allow" => ["Authorization", "Content-Type"],
    "headers.expose" => [],
    "credentials" => true,
    "cache" => 0,
]));
