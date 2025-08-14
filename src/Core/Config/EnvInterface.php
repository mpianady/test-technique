<?php declare(strict_types=1);

namespace Core\Config;

interface EnvInterface
{
    public function string(string $key, ?string $default = null): ?string;

    public function bool(string $key, bool $default = false): bool;

    public function int(string $key, int $default = 0): int;

    public function float(string $key, float $default = 0.0): float;
}
