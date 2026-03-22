<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Helpers\Seo;
use App\Core\Http\Attributes\Get;
use App\Services\ArticleService;
use App\Services\ProjectService;

class SitemapController extends Controller
{
    #[Get('/sitemap.xml', 'sitemap')]
    public function index(): void
    {
        $baseUrl = Seo::baseUrl();

        $staticPages = [
            ['url' => '/', 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['url' => '/portfolio', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['url' => '/progetti', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['url' => '/tech-stack', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['url' => '/partners', 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['url' => '/certificati', 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['url' => '/contatti', 'priority' => '0.5', 'changefreq' => 'monthly'],
        ];

        $projects = ProjectService::getActive();
        $articles = ArticleService::getActive();

        header('Content-Type: application/xml; charset=utf-8');
        mvc()->view->setLayout('raw');

        view('sitemap', compact('baseUrl', 'staticPages', 'projects', 'articles'));
    }
}
