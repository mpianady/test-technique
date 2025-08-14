<?php declare(strict_types=1);

namespace Core\Routing;

use Core\Infrastructure\Container;
use ReflectionException;
use RuntimeException;

/**
 * ControllerResolver is responsible for resolving and invoking controllers.
 * It can handle both string-based controller names and callable controllers.
 * It also supports passing parameters to the controller methods.
 */
final class ControllerResolver
{
    public function __construct(private readonly Container $container)
    {
    }

    /**
     * @throws ReflectionException
     */
    public function handle(string|callable $controller, array $params = []): void
    {
        if (is_string($controller)) {
            $controller = $this->container->get($controller);
        }
        if (!is_callable($controller)) {
            throw new RuntimeException("Controller is not callable.");
        }
        $response = $controller(...array_values($params));
        if (is_string($response)) {
            echo $response;
        }
    }
}
