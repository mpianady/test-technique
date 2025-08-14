<?php declare(strict_types=1);

namespace Map\Infrastructure\Geo;

use Map\Contracts\GeocoderInterface;
use Map\Domain\ValueObject\Coordinates;

/**
 * GoogleGeocoder is an implementation of GeocoderInterface that uses the Google Maps Geocoding API
 * to convert city names into geographical coordinates.
 *
 * It requires a valid Google Maps API key and supports optional components to refine the geocoding process.
 */
final class GoogleGeocoder implements GeocoderInterface
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $language = 'fr'
    )
    {
    }

    public function geocodeCity(string $city, array $components = []): ?Coordinates
    {
        $components = array_merge(['country:RE'], $components);
        $url = 'https://maps.googleapis.com/maps/api/geocode/json'
            . '?address=' . rawurlencode($city)
            . '&components=' . rawurlencode(implode('|', $components))
            . '&language=' . rawurlencode($this->language)
            . '&key=' . rawurlencode($this->apiKey);

        $json = @file_get_contents($url);
        if ($json === false) return null;

        $data = json_decode($json, true);
        if (($data['status'] ?? '') !== 'OK') return null;

        $loc = $data['results'][0]['geometry']['location'] ?? null;
        if (!isset($loc['lat'], $loc['lng'])) return null;

        return new Coordinates((float)$loc['lat'], (float)$loc['lng']);
    }
}
