<?php
/**
 * File entry point
 */

 // Compoeser 
require_once __DIR__.'/vendor/autoload.php';

use \App\Core\Mvc;
use App\Core\Config;



/**
 * Caricamento configurazioni dell'applicazione
 */
Config::env(__DIR__.'/.env'); // caricamento variabili d'ambiente del file .env

// istanza per la configurazione
/**
 * Due file principali per la connfigurazione, folder.php e routes.php
 * 
 * $config è un oggetto con tre elementi:
 * un elemento con chiave folder e un elemento con chiave ruote
 * quindi associamo il nome del file (esclusa estensione) alla chiave dell'elemento
 *  
 * il secondo elemento sono le routes del file routes.php:
 * con all'interno un array associativo
 * 
 * il terzo elemento è un array contenente il menu che ha come chiave la path e come valore il nome del link
 */
$config = Config::dir(__DIR__.'/config');

// istanza Mvc che è il CORE dell'architettura MVC
/**
 * Passiamo l'array con i due valori:
 * folder e route
 * invocando il metodo run
 */
// (new Mvc($config))->run();

echo 'ciaooo';