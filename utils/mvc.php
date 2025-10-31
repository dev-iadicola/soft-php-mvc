<?php 

use App\Core\Mvc;
use App\Core\Support\Collection\BuildAppFile;
if (!function_exists('inizializeMvc')) {
    /**
     * Summary of setMvc:
     * It allows you to initialize the MVC Pattern as well as make access to the instance globally.
     * @deprecated non verrà più utilizzato, si inizializza in modo OOP non con metodi globali.
     * @param App\Core\Support\Collection\BuildAppFile $config
     * @return void
     */
    function inizializeMvc(BuildAppFile $config)
    {
        $mvc = new Mvc($config);
        $GLOBALS['mvc'] = $mvc;
        $mvc->run();
    }
}

/**
 * Setta l'mvc rendendolo globale
 */
if (!function_exists('setMvc')) {
    function setMvc(Mvc $mvc)
    {
        $GLOBALS['mvc'] = $mvc;
    }
}

if (!function_exists('mvc')) {
    /**
     * Summary of mvc
     * This function allows to access the MVC istance, which is important and necessary for many framework operations
     * @return Mvc
     */
    function mvc()
    {
        return $GLOBALS['mvc'] ?? null;
    }
}