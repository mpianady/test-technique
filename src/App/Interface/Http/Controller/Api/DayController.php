<?php declare(strict_types=1);

namespace App\Interface\Http\Controller\Api;

use Exception;
use Map\Application\GetDayWeatherMapHandler;

/**
 * DayController is responsible for handling requests to retrieve
 * weather data for a specific day. It uses the GetDayWeatherMapHandler
 * use case to fetch the weather information based on the provided date.
 */
final class DayController
{
    public function __construct(private readonly GetDayWeatherMapHandler $uc)
    {
    }


    /**
     * @throws Exception
     */
    public function __invoke(): void
    {
        $date = $_GET['date'] ?? '2010-02-18';
        $payload = ($this->uc)($date);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }
}
