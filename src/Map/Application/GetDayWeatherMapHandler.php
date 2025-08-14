<?php declare(strict_types=1);

namespace Map\Application;

use DateTimeImmutable;
use Exception;
use Map\Contracts\WeatherRepositoryInterface;
use Map\Contracts\CoordinatesProviderInterface;

/**
 * GetDayWeatherMapHandler is responsible for retrieving weather data for a specific day
 * and formatting it for use in a map view.
 */
final class GetDayWeatherMapHandler
{
    public function __construct(
        private readonly WeatherRepositoryInterface   $repo,
        private readonly CoordinatesProviderInterface $coords
    )
    {
    }

    /**
     * @throws Exception
     */
    public function __invoke(string $isoDate): array
    {
        $dateFormatted = (new DateTimeImmutable($isoDate))
            ->setTime(0, 0, 0, 0)
            ->format('Y-m-d H:i:s.u');
        $points = $this->repo->getDay($isoDate);
        $out = [];

        foreach ($points as $p) {
            $c = $this->coords->byCityId($p->cityId);
            if (!$c) continue;
            $out[] = [
                'id' => $p->cityId,
                'test' => $isoDate,
                'title' => $p->cityId,
                'lat' => $c->lat,
                'lng' => $c->lng,
                'tMin' => $p->tMin,
                'tMax' => $p->tMax,
                'code' => $p->code,
                'wind' => [$p->windDir, $p->windForce],
                'hum' => $p->humidity,
                'date' => $dateFormatted,
            ];
        }

        return $out;
    }
}
