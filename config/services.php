<?php declare(strict_types=1);

use App\Interface\Http\Controller\Api\DayController;
use App\Interface\Http\Controller\Api\ForecastController;
use App\Interface\Http\Controller\MapController;
use App\Interface\View\View;
use Core\Assets\AssetManager;
use Core\Config\Env;
use Core\Config\EnvInterface;
use Core\Infrastructure\Container;
use Map\Application\GetDayWeatherMapHandler;
use Map\Application\GetNextDaysForecastHandler;
use Map\Contracts\CoordinatesProviderInterface;
use Map\Contracts\WeatherRepositoryInterface;
use Map\Infrastructure\Geo\FileCoordinatesCache;
use Map\Infrastructure\Geo\GoogleGeocoder;
use Map\Infrastructure\Geo\MixedCoordinatesProvider;
use Map\Infrastructure\IO\LocalFileJsonReader;
use Map\Infrastructure\MeteoJson\MeteoJsonWeatherRepository;

/**
 * This class is responsible for configuring the application's dependency injection container.
 * It sets up all service dependencies, repositories, controllers and infrastructure components
 * needed for the weather map application to function. The configuration is organized into
 * separate concerns like infrastructure, repositories, coordinates services, use cases,
 * view layer and controllers.
 */
final class ContainerConfigurator
{
    private readonly string $rootDir;
    private readonly string $varDir;
    private readonly string $templatesDir;
    private readonly string $meteoJsonPath;
    private readonly string $coordsCachePath;
    private readonly bool $allowOnlineGeocode;
    private readonly string $googleMapsApiKey;
    private readonly string $publicDir;

    public function __construct(?string $rootDir = null)
    {
        $this->rootDir = $rootDir ?? dirname(__DIR__);
        $this->publicDir = $this->rootDir . '/public';
        $this->varDir = $this->rootDir . '/var';
        $this->templatesDir = $this->rootDir . '/templates';
        $this->meteoJsonPath = $this->varDir . '/media/meteo.json';
        $this->coordsCachePath = $this->varDir . '/coords-cache.json';
        $this->allowOnlineGeocode = (getenv('ALLOW_ONLINE_GEOCODE') ?: 'false') === 'true';
        $this->googleMapsApiKey = getenv('GOOGLE_MAPS_API_KEY') ?: '';
    }

    public function configure(): Container
    {
        $container = new Container();

        $this->registerInfrastructure($container);
        $this->registerRepositories($container);
        $this->registerCoordinatesServices($container);
        $this->registerUseCases($container);
        $this->registerView($container);
        $this->registerControllers($container);

        return $container;
    }

    private function registerInfrastructure(Container $container): void
    {
        // Env
        $container->singleton(EnvInterface::class, fn() => new Env());

        // IO
        $container->singleton(
            LocalFileJsonReader::class,
            fn() => new LocalFileJsonReader($this->meteoJsonPath)
        );
    }

    private function registerRepositories(Container $container): void
    {
        $container->singleton(
            WeatherRepositoryInterface::class,
            fn($c) => new MeteoJsonWeatherRepository(
                $c->get(LocalFileJsonReader::class)
            )
        );
    }

    private function registerCoordinatesServices(Container $container): void
    {
        $container->singleton(
            FileCoordinatesCache::class,
            fn() => new FileCoordinatesCache($this->coordsCachePath)
        );

        $container->singleton(
            CoordinatesProviderInterface::class,
            function ($c) {
                $reader = $c->get(LocalFileJsonReader::class);
                $cache = $c->get(FileCoordinatesCache::class);
                $geocoder = $this->createGeocoder();

                return new MixedCoordinatesProvider(
                    reader: $reader,
                    cache: $cache,
                    geocoder: $geocoder,
                    allowOnlineGeocode: $this->allowOnlineGeocode
                );
            }
        );
    }

    private function registerUseCases(Container $container): void
    {
        $container->singleton(
            GetDayWeatherMapHandler::class,
            fn($c) => new GetDayWeatherMapHandler(
                $c->get(WeatherRepositoryInterface::class),
                $c->get(CoordinatesProviderInterface::class)
            )
        );

        $container->singleton(
            GetNextDaysForecastHandler::class,
            fn($c) => new GetNextDaysForecastHandler(
                $c->get(WeatherRepositoryInterface::class)
            )
        );
    }

    private function registerView(Container $container): void
    {
        $manifest = $this->publicDir . '/assets/manifest.json';

        // AssetManager (base URL configurable via env si tu veux)
        $baseUrl = getenv('ASSETS_BASE_URL') ?: '/';
        $container->singleton(
            AssetManager::class,
            fn() => new AssetManager(
                $this->publicDir,
                $baseUrl,
                is_file($manifest) ? $manifest : null
            )
        );

        $container->singleton(
            View::class,
            fn($c) => (function () use ($c) {
                $view = new View(
                    $this->templatesDir,
                    ['asset' => $c->get(AssetManager::class)]
                );

                $view->addGlobal('insert',  fn(string $tpl, array $vars = []) => $view->insert($tpl, $vars));

                return $view;
            })()
        );



    }

    private function registerControllers(Container $container): void
    {
        $container->set(
            MapController::class,
            fn($c) => new MapController(
                $c->get(View::class),
                $c->get(EnvInterface::class)
            )
        );

        $container->set(
            DayController::class,
            fn($c) => new DayController(
                $c->get(GetDayWeatherMapHandler::class)
            )
        );

        $container->set(
            ForecastController::class,
            fn($c) => new ForecastController(
                $c->get(GetNextDaysForecastHandler::class)
            )
        );
    }

    private function createGeocoder(): ?GoogleGeocoder
    {
        return ($this->allowOnlineGeocode && $this->googleMapsApiKey !== '')
            ? new GoogleGeocoder($this->googleMapsApiKey, 'fr')
            : null;
    }
}

return function (): Container {
    return (new ContainerConfigurator())->configure();
};