<?php

namespace App\Core\Http;

use App\Core\Facade\Session;
use \App\Core\View;
use Exception;

class Response
{

    private string $content = '';
    private int $statusCode = 200;
    private array $headers = [];
    private ?string $_redirectTo = null;

    public function __construct(
        public View $view
    ) {}

    /**
     * Summary of getContent
     * @return string resituisce il contenuto corrente
     */
    public function getContent()
    {
        return $this->content;
    }


    public function send(): void
    {
        if (!is_null($this->_redirectTo)) {
            foreach ($this->headers as $k => $v) {
                header("$k: $v", true);
            }
            header("Location: {$this->_redirectTo}", true, $this->statusCode);
            exit;
        }
        foreach ($this->headers as $key => $val) {
            header("$key: $val", TRUE);
        }

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

    public function redirect(?string $toUrl = '/', $status = 302): self
    {
        $this->_redirectTo = $toUrl;
        $this->setCode($status);
        return $this;
    }
    /**
     * * per flash session veloci da impostare
     * 
     */

    public function withSuccess(string $message): void
    {
        Session::setFlash('success', $message);
    }

    public function withWarning(string $message): void
    {
        Session::setFlash('warning', $message);
    }

    public function withError(string $message): void
    {
        Session::setFlash('error', $message);
    }

    public function with(string $key, string $message)
    {
        Session::setFlash($key, $message);
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

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function setCode(int $code): void
    {
        $this->statusCode = $code;
    }
    public function back(?int $code = 302): self
    {
        $this->setCode($code);
        $this->_redirectTo = mvc()->request->getLastUri();
        return $this;
    }



    public function set404($e): void
    {
        $this->setCode($e->getCode());
        $this->setContent(
            $this->view->render('error', [
                'code' => $e->getCode(),
                'errorMsg' => $e->getMessage()
            ])
        );
    }

    public function set405()
    {
        $errorContent = $this->view->render('error', [
            'code' => 405,
            'errorMsg' => 'Method not allowed.'
        ]);

        // Imposta il codice di stato e il contenuto della risposta
        $this->setCode(413);
        $this->setContent($errorContent);
    }

    public function set413(): void
    {
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

    public function set419(?string $errorMsg = null): static
    {
      
        $this->setCode(419);
       
        $errorContent = $this->view->render('error', [
            'code' => 419,
            'errorMsg' => $errorMsg ?? 'Invalid CSRF token.'
        ]);
        $this->setContent($errorContent);
        return $this;
    }

    public function set500(?string $errorMsg = 'Server Error.', int $status = 500){
          $this->setCode($status);
          $errorContent = $this->view->render('error', [
            'code' => $status,
            'errorMsg' => $errorMsg
        ]);
        $this->setContent($errorContent);
        return $this;
    }
    // If SMTP exception, do this.
      public function set550(?string $errorMsg = 'Server Error.', int $status = 550){
          $this->setCode($status);
          $errorContent = $this->view->render('error', [
            'code' => $status,
            'errorMsg' => $errorMsg
        ]);
        $this->setContent($errorContent);
        return $this;
    }

    public function setErrorHandle(string $errorMsg, int $status){
        $this->setCode($status);
         $errorContent = $this->view->render('error', [
            'code' => $status,
            'errorMsg' => $errorMsg
        ]);
        $this->setContent($errorContent);
        return $this;
    }
}
