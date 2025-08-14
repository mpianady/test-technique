<?php declare(strict_types=1);

namespace Core\Config;

final class Env implements EnvInterface
{
    /** @var array<string,string> */
    private array $vars;

    public function __construct(array $vars = [])
    {
        $this->vars = array_merge($_SERVER, $_ENV, $vars);
    }

    private function raw(string $key): ?string
    {
        $v = $this->vars[$key] ?? null;
        return is_string($v) ? $v : null;
    }

    public function string(string $key, ?string $default = null): ?string
    {
        $v = $this->raw($key);
        return $v !== null ? $v : $default;
    }

    public function bool(string $key, bool $default = false): bool
    {
        $v = $this->raw($key);
        if ($v === null) return $default;
        $v = strtolower(trim($v));
        return in_array($v, ['1', 'true', 'yes', 'on'], true);
    }

    public function int(string $key, int $default = 0): int
    {
        $v = $this->raw($key);
        return (is_numeric($v)) ? (int)$v : $default;
    }

    public function float(string $key, float $default = 0.0): float
    {
        $v = $this->raw($key);
        return (is_numeric($v)) ? (float)$v : $default;
    }
}
