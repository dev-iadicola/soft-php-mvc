<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Http\Attributes\Get;
use App\Services\CertificateService;
use App\Services\PartnerService;
use App\Services\ProjectService;
use App\Services\TechnologyService;

class PortfolioController extends Controller
{
    #[Get('portfolio')]
    public function index(): void
    {
        $projects = ProjectService::getAll();
        $certificati = CertificateService::getAll();
        $partners = PartnerService::getAll();
        $technologies = TechnologyService::getAll();

        view('portfolio', compact('projects', 'certificati', 'partners', 'technologies'));
    }
}
