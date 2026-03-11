<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Http\Attributes\Get;
use App\Services\LawService;

class LawController extends Controller {

    #[Get('/cookie')]
    public function home(): void {
        $laws = LawService::getAll();
        view('laws.law',compact('laws'));
    }

    #[Get('/laws')]
    public function index(): void
    {
        $laws = LawService::getAll();
        view('laws.law',compact('laws'));
    }
}
