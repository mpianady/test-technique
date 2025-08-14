<?php declare(strict_types=1);

/**
 * This variable serves as a Dependency Injection Container (DIC)
 * Holds service definitions and their dependencies for the application
 */
$container = (require __DIR__ . '/../config/services.php')();
$router    = (require __DIR__ . '/../config/routes.php')($container);

return [
    'container' => $container,
    'router'    => $router,
];
