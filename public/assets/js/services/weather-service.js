import {MapConfig} from '../config/map-config.js';

/**
 * WeatherService provides methods to normalize weather codes and retrieve weather types.
 * It uses a predefined map of weather codes to types for normalization and retrieval.
 */
export class WeatherService {

    static normalizeCode(code) {
        return String(code ?? '').padStart(2, '0');
    }

    static getWeatherType(code) {
        const normalized = WeatherService.normalizeCode(code);
        return MapConfig.WEATHER_CODE_MAP.get(normalized) ?? MapConfig.DEFAULT_WEATHER_TYPE;
    }
}
