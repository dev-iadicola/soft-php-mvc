<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Http\Attributes\RouteAttr;
use App\Core\Controllers\Controller;
use App\Services\LawService;

class LawController extends Controller {

    #[RouteAttr('/cookie')]
    public function home(): void {
        $laws = LawService::getAll();
        view('laws.law',compact('laws'));
    }

    #[RouteAttr('/laws')]
    public function index(): void
    {
        $laws = LawService::getAll();
        view('laws.law',compact('laws'));
    }
}
