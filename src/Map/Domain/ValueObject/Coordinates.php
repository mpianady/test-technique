<?php declare(strict_types=1);

namespace Map\Domain\ValueObject;

/**
 * Coordinates represents a geographical point defined by latitude and longitude.
 * It is used to encapsulate the coordinates of a location in a structured way.
 */
final class Coordinates
{
    public function __construct(public float $lat, public float $lng)
    {
    }
}
