<?php declare(strict_types=1);

use Core\Infrastructure\Container;
use Core\Routing\ControllerResolver;
use Core\Routing\Router;

require __DIR__ . '/../vendor/autoload.php';

$env = require __DIR__ . '/../bootstrap/env.php';
/** @var array{container: Container, router: Router} $app */
$app = require __DIR__ . '/../bootstrap/app.php';

$router   = $app['router'];
$container= $app['container'];

$match = $router->match($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
if (!$match) {
    http_response_code(404);
    echo 'Not found';
    exit;
}

$resolver = new ControllerResolver($container);
$resolver->handle($match['handler'], $match['params']);
