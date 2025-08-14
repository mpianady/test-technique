
<!-- Templates HTML pour ForecastController -->
<template id="forecast-item-template">
    <div class="forecast-item">
        <div class="item">
            <div class="forecast-date"></div>
            <div class="item-details">
                <img src="/assets/icons/thermometer.svg" alt="Température" class="temperature-icon" />
                <div><strong class="tMax"></strong> <span class="tMin"></span></div>
            </div>
        </div>
        <div  class="item">
            <div class="wind-block item-details">
                <img src="/assets/icons/wind-1.svg" alt="Vent" class="wind-icon" />
                <span class="wind"></span>
            </div>
            <div class="humidity-block item-details">
                <img src="/assets/icons/droplet-1.svg" alt="Vent" class="wind-icon" />
                <span class="humidity"></span>
            </div>
        </div>
        <div class="weather-type-block item">
            <img class="weather-icon" width="45" height="45" />
        </div>
    </div>
</template>

<template id="weather-info-template">
    <h4>
        <span class="city">
            <img class="map-pin" alt="City" />
            <span class="city-name"></span>
        </span>
        <span class="date"></span>
    </h4>
    <div class="weather-details">
        <div class="day"></div>
        <div class="left">
            <div class="wind">
                <img class="wind-icon" alt="Vent" />
                <span class="wind-dir"></span>
                <span class="wind-force"></span>
            </div>
            <div class="droplet">
                <img class="humidity-icon" alt="Humidité" />
                <span class="humidity"></span>
            </div>
            <div class="temperature">
                <span class="max"></span>
                <span class="min"></span>
            </div>
        </div>
        <div class="right">
            <img class="weather-type-icon" alt="" />
        </div>
    </div>
</template>