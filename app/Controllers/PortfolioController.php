<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Http\Attributes\Get;
use App\Model\Curriculum;
use App\Services\CertificateService;
use App\Services\ProjectService;

class PortfolioController extends Controller {

    #[Get('portfolio')]
    public function index(): void {

        $projects = ProjectService::getAll();
        $certificati = CertificateService::getAll();
        $curriculum = Curriculum::query()->orderBy('id','DESC')->first();
        view('portfolio', compact('projects','certificati', 'curriculum'));
    }
}
