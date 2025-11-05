<?php

namespace App\Core\Provider;

use Throwable;
use Whoops\Run;
use App\Core\Helpers\Log;
use Whoops\Handler\Handler;
use Whoops\Handler\PrettyPageHandler;

/**
 * Summary of WhoopsError
 * This class configures and register Whoops, 
 * the exception handler that displays a detailed in case of error.
 */
class WhoopsProvider
{
    private Run $whoops;
    private bool $debug;

    public function __construct(bool $debug = false)
    {
        // according to the APP_DEBUG propriety of the file .env
        $this->debug = $debug;

        // Create a new instance of the Whoops main handdle, this obj coordinates the entire error handling system.
        $this->whoops = new Run();
    }

    public function register(){
        // if debug is true, significa che nell'app env APP_DEBUG e' settato a true
        if($this->debug){
            $this->setupDebugMode();
        }else{
            $this->setupProductionMode();
        }
        $this->whoops->register();
    }
 /**
     * Imposta Whoops in modalità produzione.
     * Qui non vengono mostrati i dettagli tecnici degli errori, solo una pagina generica.
     */
    private function setupProductionMode(): void
    {
        // Aggiunge un handler anonimo che cattura tutte le eccezioni.
        $this->whoops->pushHandler(function (Throwable $exception) {

            // Salva l’errore nel file di log (così lo sviluppatore può leggerlo più tardi).
            Log::exception($exception);

            // Imposta il codice di risposta HTTP a 500 (errore interno del server).
            http_response_code(500);

            // Include un file PHP che contiene la pagina di errore generica (es. “Ops! Qualcosa è andato storto”).
            // __DIR__ rappresenta la directory attuale del file corrente.
            include __DIR__ . '/../../../views/pages/errors/ops.php';

            // Restituisce la costante Handler::QUIT per indicare a Whoops di interrompere la catena di handler.
            // Questo evita che altri gestori vengano eseguiti dopo.
            return Handler::QUIT;
        });
    }
    private function setupDebugMode()
    {
        // Crea un gestore grafico di tipo PrettyPageHandler.
        // Questo genera automaticamente una pagina HTML ben formattata con stacktrace, file, variabili, ecc.
        $pageHandler = new PrettyPageHandler();

        // Aggiunge nella pagina una tabella personalizzata chiamata "Environment"
        // con alcune informazioni utili per lo sviluppatore.
        $pageHandler->addDataTable('Environment', [
            'PHP Version'        => phpversion(), // Versione attuale di PHP in uso.
            'Loaded Extensions'  => implode(', ', get_loaded_extensions()), // Tutte le estensioni PHP caricate.
            'App Mode'           => getenv('APP_ENV'), // Valore della variabile d’ambiente APP_ENV (es. local, production).
            'Debug Mode'         => 'TRUE', // Mostra che il debug è attivo.
        ]);

        // Aggiunge questo handler alla pila di Whoops.
        // Verrà eseguito per mostrare la pagina HTML dell’errore.
        $this->whoops->pushHandler($pageHandler);

        // Agiunta di un secondo handler. Viene eseguito prima del render grafico.
        // Questo serve per loggare gli errori nel file.
        $this->whoops->pushHandler(function (Throwable $exception) {
            // Registra l’eccezione nel log tramite la classe Log del framework.
            Log::exception($exception);
            // Indica a Whoops di continuare l’esecuzione con gli altri handler (es. la pagina grafica).
            return Handler::DONE;
        });
    }
}
