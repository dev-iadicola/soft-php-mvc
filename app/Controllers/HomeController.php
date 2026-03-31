<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Helpers\Seo;
use App\Core\Http\Attributes\Get;
use App\Support\Inertia\PublicPageSerializer;
use App\Services\ProjectService;
use App\Services\ArticleService;
use App\Services\CertificateService;
use App\Services\SkillService;
use App\Services\ProfileService;
use App\Services\TechnologyService;

class HomeController extends Controller
{
    #[Get('/')]
    public function index(): void
    {
        $certificati = CertificateService::getActive();
        $projects = ProjectService::getActive();
        $articles = ArticleService::getActive();
        $profiles = ProfileService::getSelected();
        $skills = SkillService::getActive();
        $technologies = TechnologyService::getActive();

        $seo = Seo::make();

        inertia('Public/Home', [
            'meta' => [
                'title' => $seo['title'],
            ],
            'page' => [
                'articles' => array_map([PublicPageSerializer::class, 'articleCard'], $articles),
                'certificates' => array_map([PublicPageSerializer::class, 'certificate'], $certificati),
                'profiles' => array_map([PublicPageSerializer::class, 'profile'], $profiles),
                'projects' => array_map([PublicPageSerializer::class, 'projectCard'], $projects),
                'skills' => array_map([PublicPageSerializer::class, 'skill'], $skills),
                'technologies' => array_map([PublicPageSerializer::class, 'technology'], $technologies),
            ],
            'seo' => array_merge($seo, [
                'structured_data' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'WebSite',
                    'name' => $seo['title'],
                    'url' => $seo['url'],
                    'description' => $seo['description'],
                ],
            ]),
        ]);
    }

    #[Get('/law')]
    public function cookie(): void
    {
        view('cookie-law');
    }
}
