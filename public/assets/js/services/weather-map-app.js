import { MapConfig } from "../config/map-config.js";
import { MapController } from "../controllers/map-controller.js";
import { APIService } from "./api-service.js";
import { ForecastController } from "../controllers/forecast-controller.js";
import {MapLoader} from "../utils/map-loader-utils.js";


/* ---------- Wait helpers ---------- */
function waitForGoogleMaps(timeoutMs = 15000) {
    return new Promise((resolve, reject) => {
        if (window.google?.maps) return resolve();
        const t0 = Date.now();
        const tick = () => {
            if (window.google?.maps) return resolve();
            if (Date.now() - t0 > timeoutMs) return reject(new Error('Google Maps non chargé'));
            setTimeout(tick, 50);
        };
        tick();
    });
}

function waitForCard(cityId, timeout = 3000) {
    return new Promise((resolve, reject) => {
        const start = Date.now();
        const check = () => {
            const card = document.querySelector(`.gmw-card[data-id="${cityId}"]`);
            if (card) return resolve(card);
            if (Date.now() - start > timeout) return reject(new Error('Card not found'));
            setTimeout(check, 50);
        };
        check();
    });
}

function waitForTiles(map, timeoutMs = 10000) {
    return new Promise((resolve) => {
        if (!map || !window.google?.maps) return resolve();
        const once = google.maps.event.addListenerOnce(map, 'tilesloaded', () => resolve());
        setTimeout(() => { google.maps.event.removeListener(once); resolve(); }, timeoutMs);
    });
}

/* ---------- App ---------- */
export class WeatherMapApp {
    constructor() {
        this.map = null;
        this.mapController = null;
        this.dayData = [];
        this.globalForecastData = {};
        this.loader = new MapLoader();
    }

    async initialize() {
        this.loader.show('Initialisation…');
        try {
            await this.waitForDependencies();

            this.loader.message('Création de la carte…');
            await this.initializeMap();

            this.loader.message('Chargement des données météo…');
            // on charge les données + on attend que la carte ait affiché ses tuiles
            await Promise.all([
                this.loadMapData(),
                waitForTiles(this.map)
            ]);

            this.loader.message('Finalisation…');
            await this.setupControllers();
            await this.simulateInitialClick();

            this.loader.hide();
        } catch (error) {
            console.error('Error initializing application:', error);
            this.loader.error('Impossible de charger la carte');
            // on ne relance pas l’erreur pour laisser le message affiché
        }
    }

    async waitForDependencies() {
        await waitForGoogleMaps();
    }

    async initializeMap() {
        const mapElement = document.getElementById('map');
        if (!mapElement) throw new Error("#map element not found.");

        this.map = new google.maps.Map(mapElement, {
            center: MapConfig.MAP_CENTER,
            zoom: MapConfig.MAP_ZOOM,
            mapTypeId: 'roadmap',
            mapId: '457722f252f365e59c739091',
            gestureHandling: 'greedy'
        });

        this.mapController = new MapController(this.map);
        await this.loadPolygons();
        this.updateLegendIcons();
    }

    async loadPolygons() {
        try {
            await this.mapController.loadPolygons();
        } catch (error) {
            console.warn('Failed to load polygons:', error);
        }
    }

    updateLegendIcons() {
        document.querySelectorAll('#legend img[data-k]').forEach(img => {
            const key = img.getAttribute('data-k');
            if (MapConfig.ICONS[key]) {
                img.src = MapConfig.ICONS[key];
            }
        });
    }

    async loadMapData() {
        await Promise.all([
            this.loadWeatherData(),
            this.loadForecastData()
        ]);
    }

    async loadWeatherData() {
        try {
            const date = new Date('2010-02-18'); // TODO: remplacer par la date courante si besoin
            const iso = date.toISOString().split('T')[0];

            this.dayData = await APIService.fetchJSON(`/api/day?date=${iso}`);
            window.dayData = this.dayData;

            this.dayData.forEach(data => {
                this.mapController.createAdvancedMarker(data);
            });
        } catch (error) {
            console.warn('Failed to load weather data:', error);
        }
    }

    async loadForecastData() {
        try {
            this.globalForecastData = await APIService.fetchJSON('/api/forecast?days=5');
            window.globalForecastData = this.globalForecastData;
        } catch (error) {
            console.warn('Failed to load forecast data:', error);
        }
    }

    async setupControllers() {
        ForecastController.init();
    }

    async simulateInitialClick() {
        const saintDenisData = this.dayData.find(d => d.title?.toUpperCase() === 'SAINT-DENIS');
        if (!saintDenisData) return;

        try {
            const card = await waitForCard('SAINT-DENIS');
            card.dispatchEvent(new CustomEvent('cardClick', {
                detail: saintDenisData,
                bubbles: true
            }));
        } catch (err) {
            console.warn('Unable to simulate click on SAINT-DENIS:', err);
        }
    }
}
