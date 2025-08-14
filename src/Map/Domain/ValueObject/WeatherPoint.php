<?php declare(strict_types=1);

namespace Map\Domain\ValueObject;

/**
 * WeatherPoint represents a weather data point for a specific city.
 * It includes minimum and maximum temperatures, weather code, wind direction,
 * wind force, and humidity.
 */
final class WeatherPoint
{
    public function __construct(
        public string  $cityId,
        public ?int    $tMin,
        public ?int    $tMax,
        public ?string $code,
        public ?string $windDir,
        public ?string $windForce,
        public ?string $humidity
    )
    {
    }
}
