<?php declare(strict_types=1);

namespace Map\Infrastructure\Geo;

use Map\Contracts\CoordinatesCacheInterface;
use Map\Domain\ValueObject\Coordinates;

/**
 * FileCoordinatesCache implements CoordinatesCacheInterface to provide a simple
 * file-based cache for geographical coordinates associated with city IDs.
 * It reads from and writes to a JSON file, ensuring that coordinates can be
 * retrieved and stored efficiently.
 */
final class FileCoordinatesCache implements CoordinatesCacheInterface
{
    /** @var array<string,array{lat:float,lng:float}> */
    private array $store = [];

    public function __construct(private readonly string $path)
    {
        if (is_file($this->path)) {
            $raw = file_get_contents($this->path) ?: '{}';
            $data = json_decode($raw, true) ?: [];
            foreach ($data as $k => $v) {
                if (isset($v['lat'], $v['lng'])) {
                    $this->store[$k] = ['lat' => (float)$v['lat'], 'lng' => (float)$v['lng']];
                }
            }
        }
    }

    public function get(string $cityId): ?Coordinates
    {
        $k = strtoupper($cityId);
        if (!isset($this->store[$k])) return null;
        $p = $this->store[$k];
        return new Coordinates($p['lat'], $p['lng']);
    }

    public function put(string $cityId, Coordinates $c): void
    {
        $k = strtoupper($cityId);
        $this->store[$k] = ['lat' => $c->lat, 'lng' => $c->lng];
        $tmp = $this->path . '.tmp';
        file_put_contents($tmp, json_encode($this->store, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        rename($tmp, $this->path);
    }
}
