<?php

declare(strict_types=1);

namespace App\Core\Http;

use App\Core\Facade\Session;
use App\Core\Support\ErrorMessageFormatter;
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
    public function getContent(): string
    {
        return $this->content;
    }


    public function send(): static
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
        return $this;
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

    public function redirect(?string $toUrl = '/', int $status = 302): self
    {
        $this->_redirectTo = $toUrl;
        $this->setCode($status);
        return $this;
    }
    /**
     * * per flash session veloci da impostare
     * 
     */

    public function withSuccess(string $message): static
    {
        Session::setFlash('success', $message);
        return $this;
    }

    public function withWarning(string $message): static
    {
        Session::setFlash('warning', $message);
        return $this;
    }

    public function withError(string|array $message): static
    {
        Session::setFlash('error', ErrorMessageFormatter::format($message));
        return $this;
    }

    public function with(string $key, string $message): static
    {
        Session::setFlash($key, $message);
        return $this;
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



    public function set404(\Throwable $e): void
    {
        $this->setCode($e->getCode());
        $this->setContent(
            $this->view->render('error', [
                'code' => $e->getCode(),
                'errorMsg' => $e->getMessage()
            ])
        );
    }

    public function set405(): static
    {
        $errorContent = $this->view->render('error', [
            'code' => 405,
            'errorMsg' => 'Method not allowed.'
        ]);

        // Imposta il codice di stato e il contenuto della risposta
        $this->setCode(405);
        $this->setContent($errorContent);
        return $this;
    }

    public function set413(): static
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
        return $this;
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

    public function set500(?string $errorMsg = 'Server Error.', int $status = 500): static {
          $this->setCode($status);
          $errorContent = $this->view->render('error', [
            'code' => $status,
            'errorMsg' => $errorMsg
        ]);
        $this->setContent($errorContent);
        return $this;
    }
    // If SMTP exception, do this.
      public function set550(?string $errorMsg = 'Server Error.', int $status = 550): static {
          $this->setCode($status);
          $errorContent = $this->view->render('error', [
            'code' => $status,
            'errorMsg' => $errorMsg
        ]);
        $this->setContent($errorContent);
        return $this;
    }

    public function setErrorHandle(string $errorMsg, int $status): static {
        $this->setCode($status);
         $errorContent = $this->view->render('error', [
            'code' => $status,
            'errorMsg' => $errorMsg
        ]);
        $this->setContent($errorContent);
        return $this;
    }
}
