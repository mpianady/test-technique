<?php declare(strict_types=1);

namespace Map\Domain\Entity;

/**
 * Location represents a geographical location with an identifier, latitude, longitude, and title.
 * It is used to encapsulate the details of a specific place on the map.
 */
final class Location
{
    public function __construct(
        public string $id,
        public float  $lat,
        public float  $lng,
        public string $title
    )
    {
    }
}
