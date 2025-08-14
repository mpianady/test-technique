<?php declare(strict_types=1);

namespace Map\Contracts;

use Map\Domain\ValueObject\Coordinates;

/**
 * GeocoderInterface defines the contract for a geocoding service that can convert
 * city names into geographical coordinates. It allows for optional components to
 * refine the geocoding process.
 */
interface GeocoderInterface
{
    public function geocodeCity(string $city, array $components = []): ?Coordinates;
}
