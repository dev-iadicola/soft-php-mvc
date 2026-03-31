<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Helpers\Seo;
use App\Core\Http\Attributes\Get;
use App\Support\Inertia\PublicPageSerializer;
use App\Services\TechnologyService;

class TechnologyController extends Controller
{
    #[Get('/tech-stack', 'tech-stack')]
    public function index(): void
    {
        $seo = Seo::make([
            'title' => 'Tech Stack',
            'description' => 'Le tecnologie utilizzate nei progetti: PHP, Laravel, React, Docker e molto altro.',
        ]);

        inertia('Public/TechStack', [
            'meta' => [
                'title' => $seo['title'],
            ],
            'page' => [
                'technologies' => array_map(
                    [PublicPageSerializer::class, 'technology'],
                    TechnologyService::getActive()
                ),
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
