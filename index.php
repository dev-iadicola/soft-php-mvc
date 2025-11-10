<?php

/**
 * File entry point
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Mvc;
use App\Core\Config;

/**
 * Caricamento configurazioni dell'applicazione
 */
Config::env(__DIR__ . '/.env'); // caricamento variabili d'ambiente del file .env

// istanza per la configurazione
/**
 * Configurazione dei file principali che si trovano all'interno del
 *  percorso "/config" ove all'interno sono presenti i file  per la connfigurazione, folder.php, routes.php, middleware.php, etc.
 * 
 */

$config = Config::dir(__DIR__ . '/config'); //ritorna BuildAppFile

/**
 * Passiamo l'ogggetto con i valori delle cartelle
 * New instance of MVC, and Initialize all proividers in the run method
 */
(new Mvc($config))->run();
