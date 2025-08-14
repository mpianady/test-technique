<?php declare(strict_types=1);

namespace Map\Contracts;

use Map\Domain\ValueObject\Coordinates;

/**
 * CoordinatesProviderInterface defines the contract for a service that provides
 * geographical coordinates based on a city ID. It allows retrieval of coordinates
 * for a given city identifier.
 */
interface CoordinatesProviderInterface
{
    public function byCityId(string $id): ?Coordinates;
}
