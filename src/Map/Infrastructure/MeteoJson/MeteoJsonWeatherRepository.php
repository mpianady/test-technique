<?php declare(strict_types=1);

namespace Map\Infrastructure\MeteoJson;

use Map\Contracts\WeatherRepositoryInterface;
use Map\Infrastructure\IO\LocalFileJsonReader;
use Map\Domain\ValueObject\WeatherPoint;

/**
 * MeteoJsonWeatherRepository implements the WeatherRepositoryInterface to provide
 * weather data from a JSON file formatted according to the Meteo France specifications.
 * It reads the data from a local JSON file and maps it to WeatherPoint objects.
 */
final class MeteoJsonWeatherRepository implements WeatherRepositoryInterface
{
    public function __construct(private readonly LocalFileJsonReader $reader)
    {
    }

    public function getDay(string $isoDate): array
    {
        $data = $this->reader->read();
        $town = $data['meteo']['bulletin']['ville'] ?? [];
        return $this->mapVilleArray($town);
    }

    public function getNextDays(int $days): array
    {
        $data = $this->reader->read();
        $out = [];
        $previsions = $data['meteo']['previsions']['prevision'] ?? [];
        foreach ($previsions as $idx => $prevision) {
            if ($idx >= $days) break;
            $date = $prevision['-date'] ?? null;
            $town = $prevision['ville'] ?? [];
            $out[$date ?? "unknown"] = $this->mapVilleArray($town);
        }
        return $out;
    }

    private function mapVilleArray(array $town): array
    {
        $res = [];
        foreach ($town as $row) {
            $res[] = new WeatherPoint(
                cityId: (string)($row['-id'] ?? ''),
                tMin: isset($row['-temperature_mini']) ? (int)$row['-temperature_mini'] : null,
                tMax: isset($row['-temperature_maxi']) ? (int)$row['-temperature_maxi'] : null,
                code: $row['-temps'] ?? null,
                windDir: $row['-vent_direction'] ?? null,
                windForce: $row['-vent_force'] ?? null,
                humidity: $row['-humidite'] ?? null
            );
        }
        return $res;
    }
}
