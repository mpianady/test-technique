<?php declare(strict_types=1);

namespace Map\Application;

use Map\Contracts\WeatherRepositoryInterface;

/**
 * GetNextDaysForecastHandler is responsible for retrieving the weather forecast for the next specified number of days.
 */
final class GetNextDaysForecastHandler
{
    public function __construct(private readonly WeatherRepositoryInterface $repo)
    {
    }

    public function __invoke(int $days): array
    {
        return $this->repo->getNextDays($days);
    }
}
