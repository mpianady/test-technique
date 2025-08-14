#!/usr/bin/env php
<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Map\Infrastructure\IO\LocalFileJsonReader;
use Map\Infrastructure\Geo\FileCoordinatesCache;
use Map\Infrastructure\Geo\GoogleGeocoder;

$apiKey = getenv('GOOGLE_MAPS_API_KEY') ?: '';
if ($apiKey === '') {
    fwrite(STDERR, "Missing GOOGLE_MAPS_API_KEY\n");
    exit(1);
}

$reader = new LocalFileJsonReader(__DIR__ . '/../var/media/meteo.json');
$cache  = new FileCoordinatesCache(__DIR__ . '/../var/coords-cache.json');
$geo    = new GoogleGeocoder($apiKey, 'fr');

$data = $reader->read();
$town = $data['meteo']['bulletin']['ville'] ?? [];

foreach ($town as $v) {
    $id = $v['-id'] ?? null;
    if (!$id) continue;

    if ($cache->get($id)) { echo "OK (cache) $id\n"; continue; }

    $c = $geo->geocodeCity((string)$id);
    if ($c) {
        $cache->put($id, $c);
        echo "GEOCODED $id => {$c->lat},{$c->lng}\n";
        usleep(250000); // 0.25s anti-burst
    } else {
        echo "NOT FOUND $id\n";
    }
}
echo "Seed finished.\n";
