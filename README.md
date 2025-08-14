# Weather Map — Documentation

Test Technique PHP/VanillaJS (style DDD) qui affiche la météo des communes de **La Réunion** sur une **carte Google Maps**, avec marqueurs, légende dynamique et écran de préchargement (loader).

---

## Installation

1. **Configuration du fichier d'environnement**
    - Copier le fichier `.env.sample` en `.env`
    - Remplir les variables d'environnement nécessaires dans `.env`
    - (Optionnel) Si les coordonnées ne sont pas chargées correctement à la première tentative ```GOOGLE_MAPS_API_KEY=XXXX php bin/seed-geocode.php```

2. **Docker**
    - L'application nécessite Docker pour fonctionner
    - Utiliser `bin/start.sh` pour démarrer les conteneurs
    - Utiliser `bin/stop.sh` pour arrêter les conteneurs

---

## Sommaire

- [Fonctionnalités](#fonctionnalités)
- [Pile technique](#pile-technique)
- [Architecture (vue rapide)](#architecture-vue-rapide)
    - [Domain / Contracts (Map\Contracts)](#domain--contracts-mapcontracts)
    - [Application (Map\Application)](#application-mapapplication)
    - [Infrastructure (Map\Infrastructure)](#infrastructure-mapinfrastructure)
    - [Interface (HTTP/View) (App\Interface)](#interface-httpview-appinterface)
    - [Kernel (Core)](#kernel-core)
    - [Frontend (public/assets/js/)](#frontend-publicassetsjs)
        - [main.js](#mainjs)
        - [services/weather-map-app.js](#servicesweather-map-appjs)
        - [services/api-service.js](#servicesapi-servicejs)
        - [services/weather-service.js](#servicesweather-servicejs)
        - [controllers/map-controller.js](#controllersmap-controllerjs)
        - [controllers/forecast-controller.js](#controllersforecast-controllerjs)
        - [config/map-config.js](#configmap-configjs)
        - [utils/map-loader-utils.js](#utilsmap-loader-utilsjs)
        - [utils/dom-utils.js](#utilsdom-utilsjs)

---

## Fonctionnalités

- **Google Maps** centrée sur La Réunion (centre/zoom configurables).
- **Marqueurs météo** : tMin/tMax, code météo, vent, humidité.
- **Légende dynamique** (icônes injectées côté client).
- **Préchargement (loader)** : overlay jusqu'au chargement des tuiles + données.
- **API** :
    - `GET /api/day?date=YYYY-MM-DD`
    - `GET /api/forecast?days=N`
- **Gestion d'assets** via `AssetManager` :
    - Support `manifest.json` (Vite/Rollup) si présent.
    - **Cache-busting** automatique avec `?v=<mtime>`.
    - Helpers : `<?= $asset->css(...) ?>`, `<?= $asset->js(...) ?>`.
- **Vues PHP** avec **partials** : `<?php $insert('partials/legend') ?>`.
- **Config d'env** via `.env` + wrapper typé `EnvInterface`.

---

## Pile technique

- **PHP 8+**
- **JavaScript ES Modules** (front), **Google Maps JS API**
- **Container DI maison** : `Core\Infrastructure\Container` (aucune lib externe)
- **Dotenv** : `vlucas/phpdotenv` pour charger `.env`

---

## Architecture (vue rapide)

### Domain / Contracts (Map\Contracts)

- WeatherRepositoryInterface
- GeocoderInterface
- CoordinatesCacheInterface
- CoordinatesProviderInterface

### Application (Map\Application)

- GetDayWeatherMapHandler : joint météo du jour + coordonnées → payload pour la carte
- GetNextDaysForecastHandler : prévisions multi-jours

### Infrastructure (Map\Infrastructure)

- **Météo** : MeteoJsonWeatherRepository lit var/media/meteo.json
- **Coordonnées** : MixedCoordinatesProvider = reader + cache + geocoder (optionnel, via env)
    - **FileCoordinatesCache** : Stockage des coordonnées en JSON (var/coords-cache.json)
    - **GoogleGeocoder** : Géocodage via l'API Google Maps (si API_KEY configurée)
    - **LocalFileJsonReader** : Lecture des données météo depuis var/media/meteo.json
    - **MeteoJsonWeatherRepository** : Parse et transforme les données JSON en objets Weather

### Interface (HTTP/View) (App\Interface)

- **Controllers** : MapController, DayController (/api/day), ForecastController (/api/forecast)
- **View** : View avec globals ($asset, $insert) et partials

### Kernel (Core)

- **Infrastructure\Container** : Container de dépendances (DI) maison implémentant l'injection de dépendances et la
  résolution automatique des services
- **Routing** : Gestion du routing avec Router pour le mapping URL/controllers et ControllerResolver pour
  instancier/exécuter les controllers
- **Assets** : Gestionnaire d'assets (AssetManager) avec support du manifest.json, cache-busting
  automatique via timestamps, et helpers PHP pour l'insertion du CSS/JS
- **Config** : Gestion de la configuration via variables d'environnement avec wrapper typé (EnvInterface) et chargement
  depuis .env

### Frontend (public/assets/js/)

#### main.js

Point d'entrée qui instancie WeatherMapApp et appelle initialize(). Gère les erreurs globales d'amorçage.

#### services/weather-map-app.js

Orchestrateur principal :

- attend Google Maps (waitForGoogleMaps)
- crée la carte (via MapConfig) et affiche le loader #map-loader
- charge en parallèle : météo du jour (/api/day?...) et prévisions (/api/forecast?...)
- attend tilesloaded avant de masquer le loader
- met à jour la légende depuis MapConfig.ICONS
- simule un clic initial (ex. SAINT-DENIS) pour pré-remplir l'UI
- expose this.map, this.dayData, this.globalForecastData

#### services/api-service.js

Abstraction HTTP : fetchJSON(url) (header Accept: application/json, vérification res.ok, parse JSON, erreurs parlantes).

#### services/weather-service.js

Service de gestion de la météo :

- getWeatherData() : récupère les données météo du jour
- getForecastData() : récupère les prévisions météo

#### controllers/map-controller.js

Contrôleur Google Maps :

- createAdvancedMarker(point) : création de marqueurs météo + events
- loadPolygons() : charge et dessine les polygones (zones/communes) si fournis
- encapsule l'API Maps pour garder WeatherMapApp simple

#### controllers/forecast-controller.js

Gère l'affichage des prévisions (panneau #city-forecast) :

- init() : branche les écouteurs (ex. cardClick)
- renderForecast(cityId, data) : affiche/rafraîchit le détail d'une ville

#### config/map-config.js

Constantes front :

- MAP_CENTER, MAP_ZOOM, éventuellement MAP_STYLE/MAP_ID
- ICONS : mapping clé → URL ou data: des icônes
- chemins de data (GeoJSON) si besoin

#### utils/map-loader-utils.js

Utilitaires loader : montrer/masquer #map-loader, mise à jour de message, aria-busy.

#### utils/dom-utils.js

Utilitaires DOM : manipulation du DOM, sélecteurs, événements.