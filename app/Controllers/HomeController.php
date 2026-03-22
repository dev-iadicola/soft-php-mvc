<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Helpers\Seo;
use App\Core\Http\Attributes\Get;
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

        view('home', compact(
            'articles',
            'certificati',
            'projects',
            'profiles',
            'skills',
            'technologies',
            'seo'
        ));
    }

    #[Get('/law')]
    public function cookie(): void
    {
        view('cookie-law');
    }
}
