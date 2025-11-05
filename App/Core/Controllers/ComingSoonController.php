<?php 
namespace App\Core\Controllers;

use App\Core\Http\Attributes\RouteAttr;

class ComingSoonController extends BaseController {

    #[RouteAttr("coming-soon")]
    public function comingSoon(){
        // * Configura la pagina di manutenzione all'interno della del file config\config.php
       return  view(mvc()->config->settings["pages"]["MAINTENANCE"]);
    }

}