<?php
namespace App\Core\Http;

class Request {
    private string $path;
    private string $method;
    private array $post;

    
    public function __construct() {
        $this->path = $this->getRequestPath();
        $this->method = $this->getRequestMethod();
        $this->post = $this->getPost();
    }

    // Cattura richiesta post
    public function getPost(): array {
    $postData = $_POST ?? [];
    $fileData = $_FILES ?? [];
    $combinedData = array_merge($postData, $fileData);
    return $combinedData;
    }
    
    // Preleva la request URI
    public function getRequestPath(): string {
        return $_SERVER['REQUEST_URI'];
    }

    // Cattura il metodo della richiesta
    public function getRequestMethod(): string {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function getBack(): string|null
    {
        // Assicurati che HTTP_REFERER sia impostato
        if (isset($_SERVER['HTTP_REFERER'])) {
            return strtolower($_SERVER['HTTP_REFERER']);
        }
        
        return null;
    }

    public function redirectBack(): never
    {
        $backUrl = $this->getBack();
        if (!empty($backUrl)) {
            header("Location: $backUrl");
            exit();
        } else {
            // Gestisci il caso in cui non c'è un URL di riferimento
            header("Location: /"); // Reindirizza alla home page o ad un'altra pagina di default
            exit();
        }
    }
}