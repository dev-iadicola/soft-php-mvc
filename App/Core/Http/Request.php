<?php

namespace App\Core\Http;

use App\Core\Helpers\Log;
use App\Traits\Getter;
use App\Traits\Setter;

class Request
{
    use Getter; use Setter;
    private string $path;
    private string $method;
    private array $post;

    private string $lastUri = '/';

    private array $attributes;

    public function __construct()
    {
        $this->path = $this->uri();
        $this->method = $this->getRequestMethod();
        $this->attributes = $this->getPost();
       
    }

    private function getValuesPostRequest(){

    }

    // Cattura richiesta post
    private function getPost($index = null): array|string|int|float
    {
        $postData = $_POST ?? [];
        $fileData = $_FILES ?? [];
      
        $combinedData = array_merge($postData, $fileData);
        if( !is_null($index) && !empty($combinedData[$index])){
            return $combinedData[$index];
        }
        return $combinedData;
    }

   
    // Preleva la request URI
    /**
     * Summary of getRequestPath
     * @deprecated utilizza il metodo uri()
     * @return string
     */
    public function getRequestPath(): string
    {
        return  parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
    }

    public function uri():string{
     
        return $_SERVER['REQUEST_URI'] ?? '/';
    }


    public function all():array{
        return $this->attributes;
    }

    // Cattura il metodo della richiesta
    public function getRequestMethod(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    public function getLastUri(): string|null
    {
        // Assicurati che HTTP_REFERER sia impostato
        if (isset($_SERVER['HTTP_REFERER'])) {
            return strtolower($_SERVER['HTTP_REFERER']);
        }
        return '/';

    }

   
}
