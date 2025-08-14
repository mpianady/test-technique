import {DOMUtils} from "../utils/dom-utils.js";
import {APIService} from "../services/api-service.js";

/**
 * MapController handles the management of the map, including loading polygons,
 */
export class MapController {
    static POLYGON_STYLE = {
        strokeColor: '#98C8D8',
        strokeOpacity: 1,
        strokeWeight: 1,
        fillOpacity: 1,
        fillColor: '#63A2B8'
    };

    static HIGHLIGHT_Z_INDEX = '99999';
    static DEFAULT_Z_INDEX = '1';

    constructor(map) {
        this.map = map;
        this.markers = new Map();
    }

    async loadPolygons(url = '/data/communes-974-la-reunion.geojson') {
        this.map.data.setStyle(MapController.POLYGON_STYLE);
        const geoJsonData = await APIService.fetchJSON(url);
        this.map.data.addGeoJson(geoJsonData);
    }

    createAdvancedMarker(weatherData) {
        const cardElement = DOMUtils.createWeatherCard(weatherData);
        const {AdvancedMarkerElement} = google.maps.marker;

        const marker = new AdvancedMarkerElement({
            map: this.map,
            position: {lat: weatherData.lat, lng: weatherData.lng},
            content: cardElement,
            title: weatherData.title ?? ''
        });

        this.registerMarker(weatherData, cardElement, marker);
        return marker;
    }

    registerMarker(weatherData, cardElement, marker) {
        setTimeout(() => {
            const parent = cardElement.closest('gmp-advanced-marker');
            if (parent) {
                this.markers.set(weatherData.id.toUpperCase(), {
                    marker,
                    element: cardElement,
                    parent
                });
            }
        }, 0);
    }

    highlightCity(cityId) {
        const targetCityId = cityId.toUpperCase();

        for (const [id, {element, parent}] of this.markers.entries()) {
            if (!parent) continue;

            const isTargetCity = id === targetCityId;
            parent.style.zIndex = isTargetCity ? MapController.HIGHLIGHT_Z_INDEX : MapController.DEFAULT_Z_INDEX;
            element.classList.toggle('gmw-highlighted', isTargetCity);
        }
    }
}