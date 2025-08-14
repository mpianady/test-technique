import {MapConfig} from "../config/map-config.js";
import {WeatherService} from "../services/weather-service.js";

/**
 * DOMUtils class provides utility methods for DOM manipulation and weather card creation.
 * It includes methods for selecting elements, creating DOM elements with various attributes,
 * and generating weather information cards with specific styling and behavior.
 * This class serves as a helper for the weather map application's UI components.
 */
export class DOMUtils {
    static $(selector) {
        return document.querySelector(selector);
    }
    
    static createElement(tag, options = {}) {
        const element = document.createElement(tag);
        if (options.className) element.className = options.className;
        if (options.textContent) element.textContent = options.textContent;
        if (options.attributes) {
            Object.entries(options.attributes).forEach(([key, value]) => {
                element.setAttribute(key, value);
            });
        }
        if (options.styles) Object.assign(element.style, options.styles);
        return element;
    }

    static createWeatherCard(weatherData) {
        const container = this.createWeatherCardContainer(weatherData);
        const img = this.createWeatherIcon(weatherData);
        const content = this.createWeatherContent(weatherData);

        container.append(img, content);
        this.handleClick(container, weatherData);

        return container;
    }
    
    static createWeatherCardContainer(weatherData) {
        const container = DOMUtils.createElement('div', {
            className: 'gmw-card',
            attributes: {'data-id': weatherData.id},
            styles: {
                display: 'flex',
                alignItems: 'center',
                position: 'relative',
                gap: '8px',
                backdropFilter: 'saturate(140%) blur(2px)',
                borderRadius: '5px',
                padding: '8px 10px',
                boxShadow: '0 2px 8px rgba(0,0,0,.08)',
                font: '500 12px/1.3 system-ui,Arial',
                color: '#fff',
                pointerEvents: 'auto',
                cursor: 'pointer'
            }
        });

        container.addEventListener('click', () => {
            container.dispatchEvent(new CustomEvent('cardClick', {
                detail: weatherData,
                bubbles: true
            }));
        });

        return container;
    }
    
    static createWeatherIcon(weatherData) {
        const iconUrl = MapConfig.ICONS[WeatherService.getWeatherType(weatherData.code)];
        return DOMUtils.createElement('img', {
            attributes: {src: iconUrl, alt: '', width: '30', height: '30'},
            styles: {flex: '0 0 auto'}
        });
    }

    static createWeatherContent(weatherData) {
        const content = DOMUtils.createElement('div', {
            styles: {display: 'flex', flexDirection: 'column', gap: '2px'}
        });

        const title = DOMUtils.createElement('div', {
            textContent: weatherData.title ?? '',
            styles: {fontWeight: '700', fontSize: '10px'}
        });

        const temps = DOMUtils.createElement('div', {
            textContent: `Min/Max: ${weatherData.tMin ?? '-'}° / ${weatherData.tMax ?? '-'}°`,
            styles: {opacity: '.9'}
        });

        content.append(title, temps);
        return content;
    }
    
    static handleClick(container, weatherData) {
        if (weatherData.id === 'SAINT-DENIS') {
            setTimeout(() => container.click(), 0);
        }
    }
}