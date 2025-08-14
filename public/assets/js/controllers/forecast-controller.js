import {MapConfig} from "../config/map-config.js";
import {WeatherService} from "../services/weather-service.js";

/**
 * ForecastController handles weather forecast display and interactions for different cities.
 * It manages city selection, weather data rendering, and forecast display in the UI.
 * The controller uses date formatting and static assets to present weather information.
 */
export class ForecastController {
    static DATE_OPTIONS = {
        weekday: 'short',
        day: 'numeric'
    };

    // Extract date formatting constants
    static DETAILED_DATE_OPTIONS = {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    };

    static FULL_WEEKDAY_OPTIONS = {
        weekday: 'long'
    };

    // Extract UI text constants
    static UI_TEXT = {
        FORECAST_HEADING: 'Prévision sur 5 jours',
        LOCALE: 'fr-FR',
        ICON: '/assets/icons/forecast.svg'
    };

    // Extract asset paths
    static ASSETS = {
        MAP_PIN: '/assets/icons/map-pin.svg',
        WIND_ICON: '/assets/icons/wind-2.svg',
        HUMIDITY_ICON: '/assets/icons/droplet-2.svg'
    };


    static init() {
        document.addEventListener('cardClick', ForecastController.handleCardClick);
    }

    static handleCardClick(event) {
        const cityId = ForecastController.extractCityId(event);
        ForecastController.highlightCityOnMap(cityId);
        const filteredForecast = ForecastController.getForecastForCity(cityId);
        const filteredDayData = ForecastController.getDayDataForCity(cityId);


        ForecastController.renderCityForecast(filteredForecast);
        if (filteredDayData[0]) {
            ForecastController.renderWeatherInfo(filteredDayData[0], cityId);
        }
    }

    static extractCityId(event) {
        const data = event.detail;
        return data.title || data.cityId;
    }

    static highlightCityOnMap(cityId) {
        if (window.app?.mapController) {
            window.app.mapController.highlightCity(cityId);
        }
    }
    static renderWeatherInfo(forecast, cityId) {
        const container = document.getElementById('weather-info');
        if (!forecast || !container) return;

        const template = document.getElementById('weather-info-template');
        const content = template.content.cloneNode(true);

        const weatherType = WeatherService.getWeatherType(forecast.code);
        const forecastDate = new Date(forecast.date);

        content.querySelector('.map-pin').src = ForecastController.ASSETS.MAP_PIN;
        content.querySelector('.city-name').textContent = cityId;

        content.querySelector('.date').textContent = forecastDate.toLocaleDateString(
            ForecastController.UI_TEXT.LOCALE,
            {
                ...ForecastController.DETAILED_DATE_OPTIONS,
                timeZone: 'Indian/Reunion'
            }
        );

        content.querySelector('.day').textContent = forecastDate.toLocaleDateString(
            ForecastController.UI_TEXT.LOCALE,
            {
                ...ForecastController.FULL_WEEKDAY_OPTIONS,
                timeZone: 'Indian/Reunion'
            }
        );


        content.querySelector('.wind-icon').src = ForecastController.ASSETS.WIND_ICON;
        content.querySelector('.wind-dir').textContent = forecast.wind[0];
        content.querySelector('.wind-force').textContent = `${forecast.wind[1]} km/h`;

        content.querySelector('.humidity-icon').src = ForecastController.ASSETS.HUMIDITY_ICON;
        content.querySelector('.humidity').textContent = `${forecast.hum}%`;

        content.querySelector('.max').textContent = `${forecast.tMax}°`;
        content.querySelector('.min').textContent = `/ ${forecast.tMin}°`;

        content.querySelector('.weather-type-icon').src = MapConfig.ICONS[weatherType];

        container.innerHTML = '';
        container.appendChild(content);
    }

    static getDayDataForCity(cityId) {
        if (!window.dayData) return [];
        return window.dayData.filter(f => f.id?.toUpperCase() === cityId?.toUpperCase());
    }
    static getForecastForCity(cityId) {
        if (!window.globalForecastData) return [];
        const allForecasts = [];
        for (const [date, forecasts] of Object.entries(window.globalForecastData)) {
            forecasts.forEach(forecast => {
                allForecasts.push({...forecast, date});
            });
        }


        return allForecasts.filter(f => f.cityId?.toUpperCase() === cityId?.toUpperCase());
    }

    static renderCityForecast(forecastList) {
        const container = document.querySelector('#city-forecast');
        container.innerHTML = '';
        const heading = document.createElement('h4');
        const icon = document.createElement('img');
        icon.src = ForecastController.UI_TEXT.ICON;
        icon.alt = 'Prévision';
        const headingText = document.createElement('span');
        headingText.textContent = ForecastController.UI_TEXT.FORECAST_HEADING;
        heading.appendChild(icon);
        heading.appendChild(headingText);
        container.appendChild(heading);

        forecastList.forEach(forecast => {
            const forecastItem = ForecastController.createForecastItem(forecast);
            container.appendChild(forecastItem);
        });
    }

    static createForecastItem(forecast) {
        const template = document.getElementById('forecast-item-template');
        const item = template.content.cloneNode(true);

        const dateContainer = item.querySelector('.forecast-date');
        dateContainer.textContent = new Date(forecast.date)
            .toLocaleDateString(ForecastController.UI_TEXT.LOCALE, {
                ...ForecastController.DATE_OPTIONS,
                timeZone: 'Indian/Reunion'
            })
            .replace('.', '');

        const icon = item.querySelector('.weather-icon');
        icon.src = MapConfig.ICONS[WeatherService.getWeatherType(forecast.code)];
        icon.alt = WeatherService.getWeatherType(forecast.code);

        item.querySelector('.tMax').textContent = forecast.tMax + '°';
        item.querySelector('.tMin').textContent = ' / ' + forecast.tMin + '°';
        item.querySelector('.wind').textContent = forecast.windDir + ' ' + forecast.windForce + ' km/h';
        item.querySelector('.humidity').textContent = forecast.humidity + '%';
        return item;
    }
}