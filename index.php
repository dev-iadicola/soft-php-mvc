<?php
/**
 * File entry point
 */

require_once __DIR__.'/vendor/autoload.php';

use App\Core\Mvc;
use App\Core\Config;

/**
 * Caricamento configurazioni dell'applicazione
 */

Config::env(__DIR__.'/.env'); // caricamento variabili d'ambiente del file .env

// istanza per la configurazione
/**
 * Configurazione dei file principali che si trovano all'interno del
 *  percorso "/config" ove all'interno sono presenti i file  per la connfigurazione, folder.php, routes.php, middleware.php
 * 
 * $config è un oggetto con tre elementi:
 */

$config = Config::dir(__DIR__.'/config'); //ritorna BuildAppFile

// istanza Mvc che è il CORE dell'architettura MVC
/**
 * Passiamo l'ogggetto con i valori delle cartelle
 * la funzione globale setMvc() permette di recuperare i dati principali del software dandoci molta flessibilità per prendere una variabile MVC.
 * Questo settaggio ci permette di accedere alle proprietà della classe MVC con il metodo mvc();
 */
$mvc = new Mvc($config);
setMvc($mvc);
$mvc->run();
// (new Mvc($config))->run();
//  inizializeMvc($config);
 
