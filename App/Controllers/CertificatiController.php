<?php

namespace App\Controllers;

use App\Core\Mvc;

use App\Core\Controllers\BaseController;
use App\Core\Controllers\Controller;
use App\Core\Http\Attributes\AttributeRoute;
use App\Core\Http\Attributes\RouteAttr;
use App\Model\Certificate;

class CertificatiController extends Controller
{

 
    #[RouteAttr('certificati')]
    public function index()
    {

        $certificati = Certificate::findAll();

        return view(page: 'corsi', variables: compact('certificati'));
    }
}
