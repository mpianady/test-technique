<?php declare(strict_types=1);

namespace Map\Contracts;

use Map\Domain\ValueObject\Coordinates;

/**
 * CoordinatesCacheInterface defines the contract for a cache that stores coordinates
 * associated with city IDs. It allows retrieval and storage of coordinates.
 */
interface CoordinatesCacheInterface
{
    public function get(string $cityId): ?Coordinates;

    public function put(string $cityId, Coordinates $c): void;
}
