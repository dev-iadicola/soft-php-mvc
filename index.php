<?php
/**
 * File entry point
 */

require_once __DIR__.'/vendor/autoload.php';

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
 * la funzione globale setMvc() inizializza l'architettura MVC
 * questa inizalizzazione ci permette di accedere alle proprietà della classe MVC con il metodo mvc();
 */
 setMvc($config);
 
