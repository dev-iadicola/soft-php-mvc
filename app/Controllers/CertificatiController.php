<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Http\Attributes\RouteAttr;
use App\Services\CertificateService;

class CertificatiController extends Controller
{

    #[RouteAttr('certificati')]
    public function index(): void
    {
        $certificati = CertificateService::getAll();

        view(page: 'corsi', variables: compact('certificati'));
    }
}
