<?php declare(strict_types=1);

namespace App\Interface\Http\Controller;

use App\Interface\View\View;
use Core\Config\EnvInterface;

/**
 * MapController is responsible for rendering the map view.
 * It retrieves the Google Maps API key and a default date,
 * then passes them to the view for rendering.
 */
final class MapController
{
    public function __construct(
        private readonly View $view,
        private EnvInterface $env
    )
    {
    }

    public function __invoke(): void
    {
        $apiKey = $this->env->string('GOOGLE_MAPS_API_KEY') ?? '';
        $date = $this->env->string('DEFAULT_DATE');

        echo $this->view->render('map', [
            'apiKey' => $apiKey,
            'date' => $date,
        ]);
    }
}