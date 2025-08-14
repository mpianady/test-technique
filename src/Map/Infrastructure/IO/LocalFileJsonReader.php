<?php declare(strict_types=1);

namespace Map\Infrastructure\IO;

use RuntimeException;

/**
 * LocalFileJsonReader reads a JSON file from the local filesystem.
 * It provides methods to read and decode the JSON content into an associative array.
 */
final class LocalFileJsonReader
{
    public function __construct(private readonly string $path)
    {
    }

    public function read(): array
    {
        if (!is_file($this->path)) {
            throw new RuntimeException("JSON not found: {$this->path}");
        }
        $raw = file_get_contents($this->path);
        if ($raw === false) throw new RuntimeException("Unable to read: {$this->path}");
        if (!mb_check_encoding($raw, 'UTF-8')) $raw = mb_convert_encoding($raw, 'UTF-8', 'auto');

        $data = json_decode($raw, true, 512, JSON_BIGINT_AS_STRING);
        if (!is_array($data)) throw new RuntimeException("Invalid JSON in {$this->path}");
        return $data;
    }
}
