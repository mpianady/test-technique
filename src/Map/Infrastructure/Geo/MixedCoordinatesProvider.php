<?php declare(strict_types=1);

namespace Map\Infrastructure\Geo;

use Map\Contracts\CoordinatesProviderInterface;
use Map\Contracts\CoordinatesCacheInterface;
use Map\Contracts\GeocoderInterface;
use Map\Domain\ValueObject\Coordinates;
use Map\Infrastructure\IO\LocalFileJsonReader;

/**
 * MixedCoordinatesProvider provides geographical coordinates for cities by first checking
 * an inline data source and then falling back to a cache or an online geocoding service.
 * It allows for both local and online retrieval of coordinates.
 */
final class MixedCoordinatesProvider implements CoordinatesProviderInterface
{
    /** @var array<string, Coordinates> */
    private array $inline = [];

    public function __construct(
        private readonly LocalFileJsonReader       $reader,
        private readonly CoordinatesCacheInterface $cache,
        private readonly ?GeocoderInterface        $geocoder = null,
        private readonly bool                      $allowOnlineGeocode = false
    )
    {
    }

    public function byCityId(string $id): ?Coordinates
    {
        $k = strtoupper($id);

        if ($this->inline === []) {
            $this->hydrateInline();
        }
        if (isset($this->inline[$k])) return $this->inline[$k];

        $c = $this->cache->get($k);
        if ($c) return $c;

        if ($this->allowOnlineGeocode && $this->geocoder) {
            $result = $this->geocoder->geocodeCity($id);
            if ($result) {
                $this->cache->put($k, $result);
                return $result;
            }
        }

        return null;
    }

    private function hydrateInline(): void
    {
        $data = $this->reader->read();
        $town = $data['meteo']['bulletin']['ville'] ?? [];
        foreach (is_array($town) ? $town : [] as $row) {
            $cityId = isset($row['-id']) ? strtoupper((string)$row['-id']) : null;
            $lat = isset($row['-lat']) ? (float)$row['-lat'] : null;
            $lng = isset($row['-lng']) ? (float)$row['-lng'] : null;
            if ($cityId && $lat !== null && $lng !== null) {
                $this->inline[$cityId] = new Coordinates($lat, $lng);
            }
        }
    }
}
