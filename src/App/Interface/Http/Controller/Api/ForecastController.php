<?php declare(strict_types=1);

namespace App\Interface\Http\Controller\Api;

use Map\Application\GetNextDaysForecastHandler;

/**
 * ForecastController is responsible for handling requests to retrieve
 * the weather forecast for the next few days. It uses the GetNextDaysForecastHandler
 * use case to fetch the forecast based on the number of days specified in the request.
 */
final class ForecastController
{
    public function __construct(private readonly GetNextDaysForecastHandler $uc)
    {
    }

    public function __invoke(): void
    {
        $days = isset($_GET['days']) ? max(1, (int)$_GET['days']) : 5;
        $payload = ($this->uc)($days);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }
}
