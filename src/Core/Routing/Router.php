<?php declare(strict_types=1);

namespace Core\Routing;

/**
 * Router is responsible for defining and matching routes.
 * It supports HTTP methods and maps paths to handlers.
 */
final class Router
{
    private array $routes = [];

    public function get(string $path, string $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function match(string $method, string $uri): ?array
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $handler = $this->routes[strtoupper($method)][$path] ?? null;
        return $handler ? ['handler' => $handler, 'params' => []] : null;
    }
}
