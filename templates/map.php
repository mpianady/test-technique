<?php
/** @var \Core\Assets\AssetManager $asset */
/** @var callable $insert */
/** @var string $date */
/** @var string $apiKey */
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Météo — La Réunion (<?= htmlspecialchars($date, ENT_QUOTES) ?>)</title>
    <?= $asset->css('assets/css/styles.css') ?>
</head>
<body>
<div class="layout">
    <header>
        <h1>Test-Tiana</h1>
    </header>
    <main>
        <div class="map-wrap">
            <div id="map" aria-busy="true" aria-live="polite"></div>

            <div class="map-loader" id="map-loader" role="status" aria-label="Chargement de la carte">
                <div class="spinner" aria-hidden="true"></div>
                <span>Chargement de la carte…</span>
            </div>
        </div>

        <div id="weather-info" class="card"></div>
        <div class="side">
            <div class="forecast card">
                <div id="city-forecast"></div>
            </div>
            <div id="legend" aria-busy="true" hidden>
                <?php $insert('partials/legend') ?>
            </div>
        </div>
    </main>
</div>
<?php $insert('partials/jsTemplates') ?>

<!-- Loading Google Maps API and displaying weather data -->
<script src="https://maps.googleapis.com/maps/api/js?key=<?= htmlspecialchars($apiKey, ENT_QUOTES) ?>&libraries=marker"
        async></script>
<?= $asset->js('assets/js/main.js', ['type' => 'module', 'defer' => true]) ?>
</body>
</html>
