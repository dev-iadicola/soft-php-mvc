<?php

namespace App\Controllers;

use App\Core\Mvc;
use App\Core\Controller;
use App\Core\Controllers\BaseController;
use App\Core\Http\Attributes\AttributeRoute;
use App\Model\Certificate;

class CertificatiController extends BaseController
{

 
    #[AttributeRoute('certificati')]
    public function index()
    {

        $certificati = Certificate::findAll();

        return view(page: 'corsi', variables: compact('certificati'));
    }
}
