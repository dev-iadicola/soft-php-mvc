<?php
namespace App\Core\Http;
use \App\Core\View;

class Response {

    private string $content = '';
    private int $statusCode = 200;

    public function __construct(
        public View $view 
    ) {}

   

    public function getContent(){
        return $this->content;
    }

    public function send(): void {
        http_response_code($this->statusCode);
        echo $this->content;
    }

    public function redirect(string $toUrl, $status = 200): never {
        header("location: $toUrl");
        $this->setCode($status);
        exit;
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