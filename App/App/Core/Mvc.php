<?php

namespace App\Core;

use App\Core\ORM;
use \App\Core\View;
use App\Mail\Mailer;
use App\Core\Middleware;
use App\Core\UploadFile;
use \App\Core\Http\Router;
use \App\Core\Http\Request;
use \App\Core\Http\Response;
use App\Core\Connection\SMTP;
use \App\Core\Connection\Database;
use App\Core\Services\SessionService;
use \App\Core\Exception\NotFoundException;
use PHPMailer\PHPMailer\Exception as ExceptionSMTP;

class Mvc{
    public static Mvc $mvc;


    // Oggetti per gestire la richiesta, la risposta, le rotte e le viste
    public Request $request;
    public Response $response; // Gestione della risposta al client
    public Router $router; // Gestione delle rotte
    public View $view; // Gestione delle viste

    public UploadFile $uploadFile;
    public \PDO $pdo; // Connessione PDO al database

    public SMTP $Smtp;
    public Mailer $mailer;

    public Middleware  $middleware; //Gestione di Autenticazione utente


    public SessionService $sessionService;
    /**
     * Costruttore della classe Mvc
     *
     * @param array $config Configurazione per l'applicazione (es. impostazioni delle routes, view, ecc.)
     */
    public function __construct(public array $config)


    {
        // Imposta l'istanza statica dell'oggetto Mvc
        self::$mvc = $this;


        // inizializza l'oggetto Request per gestire le richieste HTTP
        $this->request = new Request();

        // Inizializza l'oggetto View per gestire le viste
        $this->view = new View($this);

        $this->uploadFile = new UploadFile($this);


        // inizializza l'oggetto Response per gestire la risposta HTTP
        $this->response = new Response($this->view);

        // Inizializza l'oggetto Router per gestire il routing delle richieste
        $this->router = new Router($this);

       

        // Inizializza la connessione al database e imposta il PDO per l'ORM
        $this->getPdoConnection(); // Invochiamo la connessione
        $this->getSMTPConnection();
        $this->mailer = new Mailer($this);

        Orm::setPDO($this->pdo);

        $this->middleware = new Middleware($this, $config['middleware']);

        $this->sessionService = new SessionService();
    }

    /**
     * Crea una connessione PDO e la assegna alla proprietà $pdo
     * Se la connessione fallisce, stampa un messaggio di errore e termina l'esecuzione
     */
    private function getPdoConnection()
    {
        try {
            // Crea una nuova istanza della classe Database e assegna il PDO
            $this->pdo = (new Database())->pdo;
        } catch (\PDOException $e) {
            // Se c'è un errore di connessione, stampa il messaggio di errore e termina
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
