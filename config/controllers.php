<?php
/**
 * --------------------------------------------------------------------------
 * Controller namespace map
 * --------------------------------------------------------------------------
 * This file defines the base namespaces and their corresponding directory paths
 * for the application's controllers.
 *
 * The first entry (App\Core\Controllers) points to the framework's internal
 * default controllers and should not be removed.
 *
 * The second entry (App\Controllers) is where user-defined application
 * controllers should be placed.
 *
 * This mapping allows the autoloader and router to dynamically resolve
 * controller classes by namespace without hardcoding file paths.
 */
return [
    "App\\Core\\Controllers" => baseRoot() . "/App/Core/Controllers", # ! Default Controller, don't remove this
    "App\\Controllers" => baseRoot() . "/App/Controllers",

];
