<?php

namespace App\Controllers;
use \App\Core\Mvc;
use App\Model\Law;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Controllers\Controller;

class LawController extends Controller {

   

    #[RouteAttr('/cookie')]
    public function home(){
        $laws = Law::findAll();
        return view('laws.law',compact('laws'));
    }

}