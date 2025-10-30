<?php
namespace App\Core\Http;
use \App\Core\View;
use Exception;

class Response {

    private string $content = '';
    private int $statusCode = 200;
    private array $headers = [];

    public function __construct(
        public View $view 
    ) {}

    /**
     * Summary of getContent
     * @return string resituisce il contenuto corrente
     */
    public function getContent(){
        return $this->content;
    }

    public function send(): void {
        http_response_code($this->statusCode);
        echo $this->content;
    }
      /**
     * Ritorna una risposta JSON.
     * @param array|object $data  Dati da convertire in JSON
     * @param int          $status Codice HTTP (default 200)
     */
    public function json(array|object $data, int $status = 200): self
    {
        $this->setCode($status);
        $this->setHeader('Content-Type', 'application/json; charset=utf-8');

        $this->content = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return $this;
    }

    public function redirect(string $toUrl, $status = 200): never {
        header("location: $toUrl");
        $this->setCode($status);
        exit;
    }
    public function wantsJson(): bool
    {
        return (
            isset($_SERVER['HTTP_ACCEPT']) &&
            str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')
        ) || (
            isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        );
    }
    
     /**
     * Imposta un header personalizzato.
     */
    public function setHeader(string $key, string $value): void
    {
        $this->headers[$key] = $value;
    }
   
    public function setContent(string $content): void {
        $this->content = $content;
    }

    public function setCode(int $code): void {
        $this->statusCode = $code;
    }

    public function set404($e): void {
        $this->setCode($e->getCode());
        $this->setContent(
            $this->view->render('error', [
                'code' => $e->getCode(),
                'errorMsg' => $e->getMessage()
            ])
        );
    }

    public function set413(): void {
        // Imposta il layout desiderato
        $this->view->setLayout('default');
    
        // Renderizza la vista con l'errore 413
        $errorContent = $this->view->render('error', [
            'code' => 413,
            'errorMsg' => 'The request entity is too large.'
        ]);
    
        // Imposta il codice di stato e il contenuto della risposta
        $this->setCode(413);
        $this->setContent($errorContent);
    }



}