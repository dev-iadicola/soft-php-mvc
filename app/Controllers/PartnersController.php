<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Http\Attributes\Get;
use App\Services\PartnerService;

class PartnersController extends Controller
{
    #[Get('/partners', 'partners')]
    public function index(): void
    {
        view('partners', [
            'partners' => PartnerService::getActive(),
        ]);
    }
}
