<?php declare(strict_types=1);

namespace Core\Infrastructure;

use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use RuntimeException;
use function array_key_exists;

/**
 * Container is a simple Dependency Injection container.
 * It supports both regular bindings and singleton instances.
 * It can autowire dependencies based on type hints in constructors.
 */
final class Container
{
    /** @var array<string, Closure|string> */
    private array $bindings = [];
    /** @var array<string, Closure|object|string> */
    private array $singletons = [];

    public function set(string $id, Closure|string $factory): void
    {
        $this->bindings[$id] = $factory;
    }

    public function singleton(string $id, Closure|string $factory): void
    {
        $this->singletons[$id] = $factory;
    }

    /**
     * @throws ReflectionException
     */
    public function get(string $id)
    {
        if (array_key_exists($id, $this->singletons)) {
            $s = $this->singletons[$id];
            if (is_object($s) && !$s instanceof Closure) {
                return $s;
            }
            return $this->singletons[$id] = $this->resolve($s);
        }
        if (array_key_exists($id, $this->bindings)) {
            return $this->resolve($this->bindings[$id]);
        }
        return $this->autowire($id);
    }

    /**
     * @throws ReflectionException
     */
    private function resolve(Closure|string $factory)
    {
        return $factory instanceof Closure ? $factory($this) : $this->autowire($factory);
    }

    /**
     * @throws ReflectionException
     */
    private function autowire(string $class)
    {
        if (!class_exists($class)) {
            throw new RuntimeException("Service not found: {$class}");
        }
        $ref = new ReflectionClass($class);
        $ctor = $ref->getConstructor();
        if (!$ctor || $ctor->getNumberOfParameters() === 0) {
            return new $class();
        }
        $deps = array_map(function (ReflectionParameter $p) use ($class) {
            $t = $p->getType();
            if (!$t || $t->isBuiltin()) {
                if ($p->isDefaultValueAvailable()) return $p->getDefaultValue();
                throw new RuntimeException("Cannot autowire {$class}::\${$p->getName()} (scalar not provided)");
            }
            $dep = $t->getName();
            return $this->get($dep);
        }, $ctor->getParameters());

        return $ref->newInstanceArgs($deps);
    }
}
