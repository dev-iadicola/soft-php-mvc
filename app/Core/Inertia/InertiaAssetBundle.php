<?php

declare(strict_types=1);

namespace App\Core\Inertia;

use Throwable;

class InertiaAssetBundle
{
    /** @var array<string, array<string, mixed>>|null */
    private ?array $manifest = null;

    public function __construct(
        private string $manifestPath,
        private string $publicPath = '/',
    ) {}

    public function renderTags(string $entrypoint): string
    {
        $entry = $this->entry($entrypoint);

        if ($entry === null) {
            return '';
        }

        $tags = [];

        foreach ($this->collectCss($entrypoint) as $href) {
            $href = htmlspecialchars($href, ENT_QUOTES, 'UTF-8');
            $tags[] = "<link rel=\"stylesheet\" href=\"{$href}\">";
        }

        $script = $this->assetUrl($entry['file'] ?? null);
        if ($script !== null) {
            $script = htmlspecialchars($script, ENT_QUOTES, 'UTF-8');
            $tags[] = "<script type=\"module\" src=\"{$script}\"></script>";
        }

        return implode("\n    ", $tags);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function entry(string $entrypoint): ?array
    {
        $manifest = $this->manifest();

        return $manifest[$entrypoint] ?? null;
    }

    /**
     * @return array<string>
     */
    private function collectCss(string $entrypoint, array $visited = []): array
    {
        if (isset($visited[$entrypoint])) {
            return [];
        }

        $visited[$entrypoint] = true;
        $entry = $this->entry($entrypoint);

        if ($entry === null) {
            return [];
        }

        $css = [];

        foreach (($entry['css'] ?? []) as $file) {
            $href = $this->assetUrl($file);
            if ($href !== null) {
                $css[] = $href;
            }
        }

        foreach (($entry['imports'] ?? []) as $import) {
            if (!is_string($import)) {
                continue;
            }

            $css = array_merge($css, $this->collectCss($import, $visited));
        }

        return array_values(array_unique($css));
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function manifest(): array
    {
        if (is_array($this->manifest)) {
            return $this->manifest;
        }

        if (!is_file($this->manifestPath)) {
            $this->manifest = [];
            return $this->manifest;
        }

        try {
            $decoded = json_decode(
                (string) file_get_contents($this->manifestPath),
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            $this->manifest = is_array($decoded) ? $decoded : [];
        } catch (Throwable) {
            $this->manifest = [];
        }

        return $this->manifest;
    }

    private function assetUrl(mixed $path): ?string
    {
        if (!is_string($path) || $path === '') {
            return null;
        }

        $prefix = rtrim($this->publicPath, '/');
        $normalized = ltrim($path, '/');

        if ($prefix === '') {
            return '/' . $normalized;
        }

        return $prefix . '/' . $normalized;
    }
}
