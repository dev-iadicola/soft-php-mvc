<?php

/**
 * File entry point
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Mvc;
use App\Core\Config;

/**
 * Configuration of Enviroment file .env
 */
Config::env(__DIR__ . '/.env'); 

/**
 * Configuration of the main files located within the '/config' path. 
 * where behind has configutaion file has routes.php, middleware.php, settings.php etc.
 */
$config = Config::dir(__DIR__ . '/config'); 

/**
 * New instance of MVC, and Initialize all proividers in the run method
 */
(new Mvc($config))->run();
