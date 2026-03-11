<?php

declare(strict_types=1);

namespace App\Core\Controllers;

use App\Core\Http\Attributes\RouteAttr;

class ComingSoonController extends BaseController {

    #[RouteAttr("coming-soon")]
    public function comingSoon(): void
    {
        // * Configura la pagina di manutenzione all'interno della del file config\config.php
       view(mvc()->config->get('settings.pages.MAINTENANCE'));
    }

}
