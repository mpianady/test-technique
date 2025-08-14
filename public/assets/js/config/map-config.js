export class MapConfig {
    static MAP_CENTER = Object.freeze({lat: -21.150, lng: 55.350});
    static MAP_ZOOM = 10.5;

    static ICON_SIZE = 28;

    static ICONS = Object.freeze({
        sunny: '/assets/icons/sunny.svg',
        cloudy: '/assets/icons/cloudy.svg',
        partly: '/assets/icons/partly.svg',
        rain: '/assets/icons/rain.svg',
        storm: '/assets/icons/storm.svg',
        wind: '/assets/icons/wind.svg'
    });

    static WEATHER_CODE_MAP = new Map([
        ['02', 'partly'], ['03', 'partly'],
        ['12', 'cloudy'], ['14', 'cloudy'],
        ['15', 'rain'], ['18', 'rain'],
        ['17', 'storm']
    ]);

    static DEFAULT_WEATHER_TYPE = 'sunny';
}