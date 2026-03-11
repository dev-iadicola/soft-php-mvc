<?php

declare(strict_types=1);

namespace App\Core\CLI\Stubs;

use App\Core\Filesystem\Dir\StubDir;
use App\Core\CLI\System\Out;

/**
 * Centralizes stub loading, placeholder replacement and file generation.
 *
 * Usage:
 *   StubGenerator::make('model')
 *       ->replace(['{{CLASS}}' => 'Post', '{{TABLE}}' => 'posts'])
 *       ->saveTo('/path/to/Post.php');
 */
final class StubGenerator
{
    private string $content;

    private function __construct(string $content)
    {
        $this->content = $content;
    }

    /**
     * Load a stub by name (e.g. 'model', 'controller', 'migration', 'seeder').
     */
    public static function make(string $stub): self
    {
        $path = self::stubPath($stub);

        $content = file_get_contents($path);

        if ($content === false) {
            Out::error("Stub '{$stub}' not found at: {$path}");
            exit(1);
        }

        return new self($content);
    }

    /**
     * Replace placeholders in the stub content.
     *
     * @param array<string, string> $replacements e.g. ['{{CLASS}}' => 'Post']
     */
    public function replace(array $replacements): self
    {
        $this->content = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $this->content
        );

        return $this;
    }

    /**
     * Write the generated content to the given file path.
     * Creates parent directories if needed.
     *
     * Returns true on success, false if the file already exists.
     */
    public function saveTo(string $filePath): bool
    {
        if (file_exists($filePath)) {
            return false;
        }

        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($filePath, $this->content);

        return true;
    }

    /**
     * Return the rendered content without writing to disk.
     */
    public function render(): string
    {
        return $this->content;
    }

    /**
     * Resolve the absolute path to a stub file.
     */
    public static function stubPath(string $stub): string
    {
        return StubDir::instance()->file($stub . '.stub');
    }
}
