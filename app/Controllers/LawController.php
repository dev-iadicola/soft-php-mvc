<?php

declare(strict_types=1);

namespace App\Controllers;
use \App\Core\Mvc;
use App\Model\Law;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Controllers\Controller;

class LawController extends Controller {

   

    #[RouteAttr('/cookie')]
    public function home(): void {
        $laws = Law::query()->all();
        view('laws.law',compact('laws'));
    }
    #[RouteAttr('/laws')]
    public function index(): void
    {
        $laws = Law::query()->all();
        view('laws.law',compact('laws'));
    }
}
