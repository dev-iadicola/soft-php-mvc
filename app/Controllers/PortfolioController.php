<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Helpers\Seo;
use App\Core\Http\Attributes\Get;
use App\Support\Inertia\PublicPageSerializer;
use App\Services\CertificateService;
use App\Services\ProjectService;
use App\Services\TechnologyService;

class PortfolioController extends Controller
{
    #[Get('portfolio')]
    public function index(): void
    {
        $projects = ProjectService::getActive();
        $certificati = CertificateService::getActive();
        $technologies = TechnologyService::getActive();
        $seo = Seo::make([
            'title' => 'Portfolio',
            'description' => 'Portfolio completo: progetti, certificazioni e tecnologie utilizzate.',
        ]);

        inertia('Public/Portfolio', [
            'meta' => [
                'title' => $seo['title'],
            ],
            'page' => [
                'certificates' => array_map([PublicPageSerializer::class, 'certificate'], $certificati),
                'projects' => array_map([PublicPageSerializer::class, 'projectCard'], $projects),
                'technologies' => array_map([PublicPageSerializer::class, 'technology'], $technologies),
            ],
            'seo' => array_merge($seo, [
                'structured_data' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'CollectionPage',
                    'name' => $seo['title'],
                    'url' => $seo['url'],
                    'description' => $seo['description'],
                ],
            ]),
        ]);
    }
}
