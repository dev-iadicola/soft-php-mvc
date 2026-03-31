<?php

declare(strict_types=1);

namespace App\Core\Inertia;

use App\Core\Http\Response;

class InertiaResponseFactory
{
    public function __construct(
        private Response $response,
        private string $version = '1',
        private string $rootElementId = 'app',
        private ?InertiaAssetBundle $assetBundle = null,
        private string $entrypoint = 'frontend/app.tsx',
    ) {}

    public function render(string $component, array $props = []): Response
    {
        $page = new InertiaPage(
            component: $component,
            props: SharedProps::resolve($props),
            url: $this->resolveUrl(),
            version: $this->version,
        );

        $this->response->setHeader('Vary', 'X-Inertia');
        $this->response->setHeader('X-Inertia-Version', $this->version);

        if ($this->isInertiaRequest()) {
            $this->response->setHeader('X-Inertia', 'true');
            return $this->response->json($page->toArray());
        }

        $this->response->setHeader('Content-Type', 'text/html; charset=utf-8');
        $this->response->setContent($this->renderRootHtml($page));

        return $this->response;
    }

    private function isInertiaRequest(): bool
    {
        return strtolower((string) ($_SERVER['HTTP_X_INERTIA'] ?? '')) === 'true';
    }

    private function resolveUrl(): string
    {
        return mvc()?->request?->uri() ?? ($_SERVER['REQUEST_URI'] ?? '/');
    }

    private function renderRootHtml(InertiaPage $page): string
    {
        $payload = $page->toArray();
        $assetTags = $this->assetBundle?->renderTags($this->entrypoint) ?? '';
        $seoTags = $this->renderSeoTags($payload['props'] ?? []);
        $pageJson = json_encode(
            $payload,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        $pageJson = htmlspecialchars((string) $pageJson, ENT_QUOTES, 'UTF-8');
        $csrfToken = htmlspecialchars((string) ($payload['props']['app']['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8');
        $title = htmlspecialchars(
            (string) ($payload['props']['meta']['title'] ?? 'Soft MVC'),
            ENT_QUOTES,
            'UTF-8'
        );

        return <<<HTML
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{$csrfToken}">
    <title>{$title}</title>
    <link rel="icon" type="image/png" href="/assets/img/favicon.png">
    {$seoTags}
    {$assetTags}
</head>
<body>
    <div id="{$this->rootElementId}" data-page="{$pageJson}"></div>
</body>
</html>
HTML;
    }

    /**
     * @param array<string, mixed> $props
     */
    private function renderSeoTags(array $props): string
    {
        $seo = isset($props['seo']) && is_array($props['seo']) ? $props['seo'] : [];
        $meta = isset($props['meta']) && is_array($props['meta']) ? $props['meta'] : [];

        $title = $this->stringValue($meta, 'title') ?? $this->stringValue($seo, 'title');
        $description = $this->stringValue($seo, 'description');
        $canonical = $this->stringValue($seo, 'canonical');
        $image = $this->stringValue($seo, 'image');
        $type = $this->stringValue($seo, 'type') ?? 'website';
        $robots = $this->stringValue($seo, 'robots');
        $siteName = $this->stringValue($seo, 'site_name');
        $publishedTime = $this->stringValue($seo, 'published_time');
        $modifiedTime = $this->stringValue($seo, 'modified_time');
        $twitterCard = $this->stringValue($seo, 'twitter_card') ?? ($image !== null ? 'summary_large_image' : 'summary');

        $tags = [];

        if ($description !== null) {
            $tags[] = sprintf('<meta name="description" content="%s">', $this->escape($description));
        }

        if ($robots !== null) {
            $tags[] = sprintf('<meta name="robots" content="%s">', $this->escape($robots));
        }

        if ($canonical !== null) {
            $tags[] = sprintf('<link rel="canonical" href="%s">', $this->escape($canonical));
            $tags[] = sprintf('<meta property="og:url" content="%s">', $this->escape($canonical));
        }

        $tags[] = sprintf('<meta property="og:type" content="%s">', $this->escape($type));

        if ($title !== null) {
            $escapedTitle = $this->escape($title);
            $tags[] = sprintf('<meta property="og:title" content="%s">', $escapedTitle);
            $tags[] = sprintf('<meta name="twitter:title" content="%s">', $escapedTitle);
        }

        if ($description !== null) {
            $escapedDescription = $this->escape($description);
            $tags[] = sprintf('<meta property="og:description" content="%s">', $escapedDescription);
            $tags[] = sprintf('<meta name="twitter:description" content="%s">', $escapedDescription);
        }

        if ($image !== null) {
            $escapedImage = $this->escape($image);
            $tags[] = sprintf('<meta property="og:image" content="%s">', $escapedImage);
            $tags[] = sprintf('<meta name="twitter:image" content="%s">', $escapedImage);
        }

        if ($siteName !== null) {
            $tags[] = sprintf('<meta property="og:site_name" content="%s">', $this->escape($siteName));
        }

        if ($publishedTime !== null) {
            $tags[] = sprintf('<meta property="article:published_time" content="%s">', $this->escape($publishedTime));
        }

        if ($modifiedTime !== null) {
            $tags[] = sprintf('<meta property="article:modified_time" content="%s">', $this->escape($modifiedTime));
        }

        $tags[] = sprintf('<meta name="twitter:card" content="%s">', $this->escape($twitterCard));

        foreach ($this->structuredDataEntries($seo) as $entry) {
            $json = json_encode($entry, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            if ($json === false) {
                continue;
            }

            $tags[] = sprintf('<script type="application/ld+json">%s</script>', $json);
        }

        return implode("\n    ", $tags);
    }

    /**
     * @param array<string, mixed> $seo
     * @return array<int, array<string, mixed>>
     */
    private function structuredDataEntries(array $seo): array
    {
        $structuredData = $seo['structured_data'] ?? null;

        if (!is_array($structuredData) || $structuredData === []) {
            return [];
        }

        if (array_is_list($structuredData)) {
            return array_values(array_filter(
                $structuredData,
                static fn(mixed $entry): bool => is_array($entry)
            ));
        }

        return [$structuredData];
    }

    /**
     * @param array<string, mixed> $values
     */
    private function stringValue(array $values, string $key): ?string
    {
        $value = $values[$key] ?? null;

        if (!is_scalar($value)) {
            return null;
        }

        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
