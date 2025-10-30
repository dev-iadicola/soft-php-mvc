<?php

namespace App\Core;

use Throwable;
use Whoops\Run;
use \App\Core\View;
use App\Mail\Mailer;
use App\Core\Storage;
use \App\Core\Database;
use App\Core\Middleware;
use \App\Core\Http\Router;
use \App\Core\Http\Request;
use \App\Core\Http\Response;
use App\Core\Eloquent\Model;
use App\Core\Connection\SMTP;
use Whoops\Handler\PrettyPageHandler;
use \App\Core\Exception\NotFoundException;
use App\Core\Services\SessionStorage;
use App\Core\Support\Collection\BuildAppFile;
use PHPMailer\PHPMailer\Exception as ExceptionSMTP;

class Mvc
{
    public static Mvc $mvc;


    // Oggetti per gestire la richiesta, la risposta, le rotte e le viste
    public Request $request;
    public Response $response; // Gestione della risposta al client
    public Router $router; // Gestione delle rotte
    public Controller $controller;
    public View $view; // Gestione delle viste

    public Storage $storage;
    public \PDO $pdo; // Connessione PDO al database

    public SMTP $Smtp;
    public Mailer $mailer;

    public Middleware  $middleware; //Gestione di Autenticazione utente
    public SessionStorage $sessionStorage;
    /**
     * Costruttore della classe Mvc
     *
     * @param array $config Configurazione per l'applicazione (es. impostazioni delle routes, view, ecc.)
     */
    public function __construct(public BuildAppFile $config)
    {

        // Inizalizzazione per la debug layout
        $this->getNativeErrorInLog();
        $this->initializeWhoops();

        $this->config = $config;
        // Imposta l'istanza statica dell'oggetto Mvc
        self::$mvc = $this;
        // inizializza l'oggetto Request per gestire le richieste HTTP
        $this->request = new Request();
        // Inizializza l'oggetto View per gestire le viste
        $this->view = new View($this);
        // inizializza l'oggetto Response per gestire la risposta HTTP
        $this->response = new Response($this->view);
        // Inizializza l'oggetto Router per gestire il routing delle richieste
        $this->router = new Router($this);
        // gestione sessioni 
        $this->sessionStorage = SessionStorage::getInstance();

        $this->middleware = new Middleware($this, $config->middleware);
        // Inizializza la connessione al database e imposta il PDO per l'Model
        $this->getPdoConnection(); // Invochiamo la connessione
        $this->getSMTPConnection();

       
        $this->controller = new Controller(mvc: $this);
    }

    private function getNativeErrorInLog(): void{
        //  Intercetta anche errori PHP (warning, notice, deprecated, ecc.)
    set_error_handler(function ($errno, $errstr, $errfile, $errline) {
        // Crea un oggetto ErrorException per uniformità con Log::exception
        $exception = new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        \App\Core\Helpers\Log::exception($exception);

        // Restituisce false per lasciare proseguire i normali handler PHP/Whoops
        return false;
    });

    // Intercetta fatal error, parse error, ecc.
    register_shutdown_function(function () {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE], true)) {
            $exception = new \ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            );
            \App\Core\Helpers\Log::exception($exception);
        }
    });
    }

    //Layout per Debug
    private function initializeWhoops()
    {
        $whoops = new Run;
        if (strtolower(getenv('APP_DEBUG')) == 'true') {

            $handler = new PrettyPageHandler();
            $handler->addDataTable('Environment', [
                'PHP Version' => phpversion(),
                'Loaded Extensions' => implode(', ', get_loaded_extensions()),
                'App Mode' => getenv('APP_ENV'),
            ]);
            $whoops->pushHandler($handler);

            // Logga l'eccezione anche in debug
            $whoops->pushHandler(function ($exception, $inspector, $run) {
                \App\Core\Helpers\Log::exception($exception);
                return \Whoops\Handler\Handler::DONE; // Continua con gli altri handler
            });
        } else {
            $whoops->pushHandler(function (Throwable $exception, $inspector, $run) {
                \App\Core\Helpers\Log::exception($exception); /// logga l'eccezione in produzione
                http_response_code(500);
                include  __DIR__ . "/../../views/pages/errors/ops.php";
                return \Whoops\Handler\Handler::QUIT; // Ferma l’esecuzione di Whoops
            });
        }
        

        $whoops->register();
    }

    /**
     * Crea una connessione PDO e la assegna alla proprietà $pdo
     * Se la connessione fallisce, stampa un messaggio di errore e termina l'esecuzione
     */
    private function getPdoConnection()
    {
        try {
            $this->pdo = Database::getInstance()->getConnection();
        } catch (\PDOException $e) {
            if (getenv('CLOUD') == 'true')
                $this->response->redirect('/coming-soon');
            else
                echo "Errore di connessione al database: " . $e->getMessage();
            exit;
        }
    }

    private function getSMTPConnection()
    {
        try {
            $this->Smtp = new SMTP();
        } catch (ExceptionSMTP $e) {
            echo "Errore di connessione al servizio di posta elettronica " . $e->getMessage();
            exit;
        }
    }

    /**
     * Avvia l'applicazione, risolvendo la richiesta e inviando la risposta
     */
    public function run()
    {        
        try {
            // Risolve la richiesta, ovvero determina quale azione eseguire in base alla rotta
            $this->router->resolve();
        } catch (NotFoundException $e) {
            // Se la rotta non viene trovata, imposta una risposta 404
            $this->response->set404($e);
        }

        // Invia la risposta al client
        $this->response->send();
    }
}
