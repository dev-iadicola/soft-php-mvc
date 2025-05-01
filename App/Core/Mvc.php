<?php

namespace App\Core;

use Whoops\Run;
use App\Core\Eloquent\ORM;
use \App\Core\View;
use App\Mail\Mailer;
use App\Core\Middleware;
use App\Core\Storage;
use \App\Core\Http\Router;
use \App\Core\Http\Request;
use \App\Core\Http\Response;
use App\Core\Connection\SMTP;
use \App\Core\Connection\Database;
use App\Core\Services\SessionService;
use Whoops\Handler\PrettyPageHandler;
use \App\Core\Exception\NotFoundException;
use App\Core\Support\Tree\TreeProject;
use App\Core\Helpers\Log;

use PHPMailer\PHPMailer\Exception as ExceptionSMTP;

class Mvc{
    public static Mvc $mvc;


    // Oggetti per gestire la richiesta, la risposta, le rotte e le viste
    public Request $request;
    public Response $response; // Gestione della risposta al client
    public Router $router; // Gestione delle rotte
    public View $view; // Gestione delle viste

    public Storage $uploadFile;
    public \PDO $pdo; // Connessione PDO al database

    public SMTP $Smtp;
    public Mailer $mailer;

    public Middleware  $middleware; //Gestione di Autenticazione utente


    public SessionService $sessionService;

    private TreeProject $treeProject; // serve per la popolazione dell'albero
    /**
     * Costruttore della classe Mvc
     *
     * @param array $config Configurazione per l'applicazione (es. impostazioni delle routes, view, ecc.)
     */
    public function __construct(public array $config)


    {
        // Inizalizzazione per la debug layout
        $this->initializeWhoops();
        Log::info(['array'=>'test']);

        // Imposta l'istanza statica dell'oggetto Mvc
        self::$mvc = $this;
        

        // $this->treeProject = new TreeProject();
        

        // inizializza l'oggetto Request per gestire le richieste HTTP
        $this->request = new Request();

        // Inizializza l'oggetto View per gestire le viste
        $this->view = new View($this);

        $this->uploadFile = new Storage($this);


        // inizializza l'oggetto Response per gestire la risposta HTTP
        $this->response = new Response($this->view);

        // Inizializza l'oggetto Router per gestire il routing delle richieste
        $this->router = new Router($this);

       
        $this->middleware = new Middleware($this, $config['middleware']);
        // Inizializza la connessione al database e imposta il PDO per l'ORM
        $this->getPdoConnection(); // Invochiamo la connessione
        $this->getSMTPConnection();
        $this->mailer = new Mailer($this);

        Orm::setPDO($this->pdo);
        $this->sessionService = new SessionService();
    }

    //Layout per Debug
    private function initializeWhoops()
    {
    
        if (getenv('APP_DEBUG') === 'true') {
            $whoops = new Run;
            $handler = new PrettyPageHandler();
            $handler->addDataTable('Environment', [
                'PHP Version' => phpversion(),
                'Loaded Extensions' => implode(', ', get_loaded_extensions()),
                'App Mode' => getenv('APP_ENV'),
            ]);
            $whoops->pushHandler($handler);
            $whoops->register();
        }
    }

    /**
     * Crea una connessione PDO e la assegna alla proprietà $pdo
     * Se la connessione fallisce, stampa un messaggio di errore e termina l'esecuzione
     */
    private function getPdoConnection()
    {
        try {
            $this->pdo = (new Database())->pdo;
        } catch (\PDOException $e) {
           if( getenv('CLOUD') == 'true')
                $this->response->redirect('/coming-soon'); 
            else
                 echo "Errore di connessione al database: " . $e->getMessage();
            exit;
        }
    }

    private function getSMTPConnection(){
        try{
            $this->Smtp = new SMTP();
        }catch(ExceptionSMTP $e){
            echo "Errore di connessione al servizio di posta elettronica ". $e->getMessage();
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
