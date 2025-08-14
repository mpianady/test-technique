import {WeatherMapApp} from "./services/weather-map-app.js";

/**
 * Main entry point for the Weather Map application.
 * This script initializes the application, sets up the map, and loads necessary data.
 * It also handles the initial click simulation to display weather data.
 */
(async () => {
    'use strict';
    try {
        const app = new WeatherMapApp();
        window.app = app;
        await app.initialize();
    } catch (error) {
        console.error('Error initializing application:', error);
    }
})();