<?php declare(strict_types=1);

namespace App\Interface\View;

final class View
{
    public function __construct(
        private readonly string $basePath,
        private array $globals = []        // ← nouveau
    ) {}

    public function addGlobal(string $key, mixed $value): void   // ← optionnel
    {
        $this->globals[$key] = $value;
    }

    /**
     * @param array<string,mixed> $data
     */
    public function render(string $template, array $data = []): string
    {
        $file = rtrim($this->basePath, '/\\') . DIRECTORY_SEPARATOR . ltrim($template, '/\\') . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException("Template not found: {$file}");
        }

        $vars = array_merge($this->globals, $data);

        ob_start();
        extract($vars, EXTR_SKIP);
        require $file;
        return (string) ob_get_clean();
    }

    /** Retourne le contenu d’un partial (chaîne) */
    public function renderPartial(string $template, array $data = []): string
    {
        $file = $this->resolve($template);
        $vars = array_merge($this->globals, $data);

        ob_start();
        $this->requireWithScope($file, $vars);
        return (string) ob_get_clean();
    }

    /** Insère un partial directement (echo) */
    public function insert(string $template, array $data = []): void
    {
        echo $this->renderPartial($template, $data);
    }

    private function resolve(string $template): string
    {
        $path = rtrim($this->basePath, '/\\') . DIRECTORY_SEPARATOR
            . ltrim($template, '/\\');
        if (!str_ends_with($path, '.php')) {
            $path .= '.php';
        }
        if (!is_file($path)) {
            throw new \RuntimeException("Template not found: {$path}");
        }
        return $path;
    }

    /** Isole le scope et injecte les variables */
    private function requireWithScope(string $__file__, array $__vars__): void
    {
        extract($__vars__, EXTR_SKIP);
        require $__file__;
    }
}
