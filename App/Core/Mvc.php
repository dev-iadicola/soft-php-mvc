<?php

namespace App\Core;

use App\Core\Provider\DatabaseProvider;
use App\Core\Provider\SmtpProvider;
use PDOException;
use \App\Core\View;
use App\Mail\Mailer;
use App\Core\Storage;
use \App\Core\Database;
use App\Core\Middleware;
use \App\Core\Http\Router;
use \App\Core\Http\Request;
use \App\Core\Http\Response;
use App\Core\Connection\SMTP;
use App\Core\Services\SessionStorage;
use \App\Core\Exception\NotFoundException;
use App\Core\Provider\NativeErrorProvider;
use App\Core\Provider\WhoopsProvider;
use App\Core\Services\CsrfService;

class Mvc
{
    public static Mvc $mvc;

    // Boolean that allows displaying or hiding errors on the screen
    private bool $debugStatus = false;

    /**
     * Summary of WhoopsError
     * This class configures and register Whoops, 
     * the exception handler that displays a detailed in case of error.
     */
    private WhoopsProvider $whoopsProvider;
    private NativeErrorProvider $nativeErrorProvider;

    // Object to handle the rquest
    public Request $request;
    // to handle the response to client.
    public Response $response;
    public Router $router;
    public View $view;
    public Storage $storage;
    public \PDO $pdo;
    public SMTP $Smtp;
    public Mailer $mailer;
    public SessionStorage $sessionStorage;

    /** @var \App\Core\Support\Collection\BuildAppFile */
    public array|object $config;

    /**
     *Constructor of the foundamental class
     * @param array|object $config Configurazione per l'applicazione (es. impostazioni delle routes, view, ecc.)
     */
    public function __construct(array|object $config)
    {
        // * Enable the programs's error and crash handlers and register all error and warning in app.log file
        $this->nativeErrorProvider = new NativeErrorProvider();
        // * This class configures and register Whoops, 
        //* the exception handler that displays a detailed in case of error.
        // Questa classe configura e registra Whoops,  visualizza un messaggio dettagliato in caso di errore.
        $this->whoopsProvider = new WhoopsProvider();
        $this->whoopsProvider->register();


        // * object of the dir config.
        $this->config = $config;

        // * Inizializes the Request to handle HTTP request. Rappresent the client's HTTP request.
        $this->request = new Request();
        // * Initializes the View object to manage views.
        $this->view = new View($this);
        // * Rappresent the response that the server return.
        $this->response = new Response($this->view);
        // * Initializes the Router object to handle request routing
        $this->router = new Router($this);

        // * can access the methods of the class everywhere
        self::$mvc = $this;
    }




    /**
     * Initialize all proividers in the run method
     */
    public function run(): void
    {
        // * This class follows the singleton pattern and is responsible for managing the session.
        // * It is currently implemented by the AuthService and CsrfService  classes and Midlleware for count requests.
        $this->sessionStorage = SessionStorage::getInstance();

        // * Initializes the Database Provider, which manages the PDO connection lifecycle.  
        // * It creates a single PDO instance through the Database singleton and handles connection errors.  
        //   In case of failure, it redirects to the errore page (in production) or prints the exception (in debug mode).  
        $this->pdo = (new DatabaseProvider($this->response))->register();

        // * Initializes the SMTP Provider, which configures and manages the mail transport layer.  
        // * It creates a single mailer instance 
        //   to send transactional and system emails.  
        //   It loads SMTP credentials from environment variables and validates the connection on startup.    
        $this->Smtp = (new SmtpProvider($this->response))->register();



        // * Generate token for Csrf if is not set.
        (new CsrfService())->generateToken();


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
