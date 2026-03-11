<?php

declare(strict_types=1);

namespace App\Core\Http;

class Request
{
    private string $path;

    private string $method;

    private array $post;

    private array $files;

    private string $lastUri = '/';

    /** @var array<string, mixed> */
    private array $attributes = [];

    public function __construct()
    {
        $this->path = $this->uri();
        $this->post = $_POST ?? [];
        $this->files = $_FILES ?? [];
        $this->method = $this->getRequestMethod();
        $this->attributes = $this->post;
    }

    /**
     * Get a value from the request attributes.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    /**
     * Get a value from the request attributes as a string.
     */
    public function string(string $key, string $default = ''): string
    {
        return (string) ($this->attributes[$key] ?? $default);
    }

    /**
     * Get a value from the request attributes as an integer.
     */
    public function int(string $key, int $default = 0): int
    {
        return (int) ($this->attributes[$key] ?? $default);
    }

    /**
     * Get a value from the request attributes as a boolean.
     */
    public function bool(string $key, bool $default = false): bool
    {
        return (bool) ($this->attributes[$key] ?? $default);
    }

    /**
     * Check if the request has a given attribute key.
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Get the request path.
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * Get the HTTP method.
     */
    public function method(): string
    {
        return $this->method;
    }

    public function files(): array
    {
        return $this->files;
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function hasFile(string $key): bool
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] === UPLOAD_ERR_OK;
    }

    public function getRequestInfo(): string
    {
        $ip = $this->getIp();
        $userAgent = $this->getUserAgent();
        $method = $this->getRequestMethod();
        $uri = $this->uri();
        $referrer = $_SERVER['HTTP_REFERER'] ?? 'Direct';
        $host = $this->getHost();
        $time = date('Y-m-d H:i:s');

        $body = ! empty($_POST) ? json_encode($_POST, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : 'No body data';

        return <<<INFO
    ========= HTTP REQUEST INFO =========
      Time:        {$time}
      IP Address:  {$ip}
      Method:      {$method}
      URI:         {$uri}
      Referrer:    {$referrer}
      Host:        {$host}
      User-Agent:  {$userAgent}
      Payload:
    {$body}
    =====================================
    INFO;
    }

    public function getIp(): string
    {
        return $server['REMOTE_ADDR'] ?? 'Unknown';
    }

    public function getUserAgent(): string
    {
        return $server['HTTP_USER_AGENT'] ?? 'Unknown';
    }

    public function getHost(): string
    {
        return $server['HTTP_HOST'] ?? 'localhost';
    }

    // Preleva la request URI
    /**
     * Summary of getRequestPath
     *
     * @deprecated utilizza il metodo uri()
     */
    public function getRequestPath(): string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
    }

    // Cattura il metodo della richiesta
    public function getRequestMethod(): string
    {
        // Metodo base (GET, POST, ecc.)
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'CLI');

        // Se è POST, verifica se c’è un override
        if ($method === 'POST') {
            $override = $_POST['_method'] ?? $_GET['_method'] ?? null;

            if ($override) {
                $override = strtoupper($override);

                // Permetti solo metodi validi
                if (in_array($override, ['PUT', 'PATCH', 'DELETE'], true)) {
                    return $override;
                }
            }
        }

        return $method;
    }

    public function uri(): string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? $_SERVER['REQUEST_URI'];
    }

    public function getURI(): string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
    }

    public function all(): array
    {
        return $this->attributes;
    }

    public function getLastUri(): ?string
    {
        // Assicurati che HTTP_REFERER sia impostato
        if (isset($_SERVER['HTTP_REFERER'])) {
            return strtolower($_SERVER['HTTP_REFERER']);
        }

        return '/';
    }

    // Cattura richiesta post

}
