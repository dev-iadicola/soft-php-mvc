<?php
namespace App\Core\Http;
use \App\Core\View;

class Response {

    private string $content = '';
    private int $statusCode = 200;

    public function __construct(
        public View $view 
    ) {}

    public function send() {
        http_response_code($this->statusCode);
        echo $this->content;
    }

    public function redirect($toUrl) {
        header("location: $toUrl");
        exit;
    }

    public function setContent($content) {
        $this->content = $content;
    }

    public function setCode($code) {
        $this->statusCode = $code;
    }

    public function set404($e) {
        $this->setCode($e->getCode());
        $this->setContent(
            $this->view->render('error', [
                'code' => $e->getCode(),
                'errorMsg' => $e->getMessage()
            ])
        );
    }

    public function set413() {
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