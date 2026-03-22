<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Helpers\Seo;
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
        $projects = ProjectService::getActive();
        $certificati = CertificateService::getActive();
        $partners = PartnerService::getActive();
        $technologies = TechnologyService::getActive();
        $seo = Seo::make([
            'title' => 'Portfolio',
            'description' => 'Portfolio completo: progetti, certificazioni, partner e tecnologie utilizzate.',
        ]);

        view('portfolio', compact('projects', 'certificati', 'partners', 'technologies', 'seo'));
    }
}
