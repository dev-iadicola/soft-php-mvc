<?php

declare(strict_types=1);

namespace App\Core\Http;

use App\Core\Traits\Attributes;

class Request
{
    use Attributes;

    private string $path;

    private string $method;

    private array $post;

    private array $files;

    private string $lastUri = '/';

    public function __construct()
    {
        $this->path = $this->uri();
        $this->post = $_POST ?? [];
        $this->files = $_FILES ?? [];
        $this->method = $this->getRequestMethod();
        $this->attributes = $this->post;
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

    public function getIp()
    {
        return $server['REMOTE_ADDR'] ?? 'Unknown';
    }

    public function getUserAgent()
    {
        return $server['HTTP_USER_AGENT'] ?? 'Unknown';
    }

    public function getHost()
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
