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
    {$assetTags}
</head>
<body>
    <div id="{$this->rootElementId}" data-page="{$pageJson}"></div>
</body>
</html>
HTML;
    }
}
