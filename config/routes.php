<?php declare(strict_types=1);

use Core\Routing\Router;
use App\Interface\Http\Controller\MapController;
use App\Interface\Http\Controller\Api\DayController;
use App\Interface\Http\Controller\Api\ForecastController;

/**
 * This file defines the application's routes.
 * It returns a Router instance with the defined routes.
 *
 * @return Router
 */
return function (): Router {
    $router = new Router();
    $router->get('/', MapController::class);
    $router->get('/map', MapController::class);
    $router->get('/api/day', DayController::class);
    $router->get('/api/forecast', ForecastController::class);
    return $router;
};
