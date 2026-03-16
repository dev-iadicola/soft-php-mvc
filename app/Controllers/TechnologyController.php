<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Http\Attributes\Get;
use App\Services\TechnologyService;

class TechnologyController extends Controller
{
    #[Get('/tech-stack', 'tech-stack')]
    public function index(): void
    {
        view('technology', [
            'technologies' => TechnologyService::getActive(),
        ]);
    }
}
