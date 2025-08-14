<?php declare(strict_types=1);

namespace Core\Assets;

final class AssetManager
{
    private array $manifest = [];

    public function __construct(
        private readonly string $publicDir,          // ex: /path/to/project/public
        private readonly string $baseUrl = '/',      // ex: '/', ou 'https://cdn.exemple.com/'
        ?string $manifestPath = null                 // ex: /public/assets/manifest.json
    ) {
        if ($manifestPath && is_readable($manifestPath)) {
            $json = file_get_contents($manifestPath);
            $this->manifest = $json ? (json_decode($json, true) ?? []) : [];
        }
    }

    public function url(string $path): string
    {
        $normalized = ltrim($path, '/');

        // 1) Manifest (Vite/Rollup)
        if (isset($this->manifest[$normalized])) {
            $mapped = $this->manifest[$normalized]['file'] ?? $this->manifest[$normalized];
            return rtrim($this->baseUrl, '/') . '/' . ltrim($mapped, '/');
        }

        // 2) Fichier brut + cache-busting via mtime
        $url = rtrim($this->baseUrl, '/') . '/' . $normalized;
        $fs  = rtrim($this->publicDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $normalized;

        if (is_file($fs)) {
            $v = (string) @filemtime($fs);
            if ($v !== '0') {
                $url .= (str_contains($url, '?') ? '&' : '?') . 'v=' . $v;
            }
        }
        return $url;
    }

    public function css(string $path, array $attrs = []): string
    {
        $href = htmlspecialchars($this->url($path), ENT_QUOTES, 'UTF-8');
        $attrStr = $this->attrs(array_merge(['rel' => 'stylesheet'], $attrs));
        return "<link href=\"{$href}\" {$attrStr}>";
    }

    public function js(string $path, array $attrs = []): string
    {
        $src = htmlspecialchars($this->url($path), ENT_QUOTES, 'UTF-8');
        $attrStr = $this->attrs($attrs);
        return "<script src=\"{$src}\" {$attrStr}></script>";
    }

    private function attrs(array $attrs): string
    {
        $parts = [];
        foreach ($attrs as $k => $v) {
            if (is_bool($v)) {
                if ($v) $parts[] = htmlspecialchars((string)$k, ENT_QUOTES, 'UTF-8');
                continue;
            }
            $parts[] = sprintf('%s="%s"',
                htmlspecialchars((string)$k, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8')
            );
        }
        return implode(' ', $parts);
    }
}
