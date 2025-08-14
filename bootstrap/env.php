<?php declare(strict_types=1);

use Dotenv\Dotenv;

$root = dirname(__DIR__);

// Charge .env sans planter si absent
$dotenv = Dotenv::createImmutable($root);
$dotenv->safeLoad();
