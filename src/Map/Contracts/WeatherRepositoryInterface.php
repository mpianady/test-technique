<?php declare(strict_types=1);

namespace Map\Contracts;

use Map\Domain\ValueObject\WeatherPoint;

/**
 * WeatherRepositoryInterface defines the contract for a repository that provides
 * weather data for specific dates and the next several days.
 */
interface WeatherRepositoryInterface
{
    /** @return WeatherPoint[] */
    public function getDay(string $isoDate): array;

    /** @return array<string, WeatherPoint[]> */
    public function getNextDays(int $days): array;
}
