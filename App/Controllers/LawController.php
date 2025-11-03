<?php

namespace App\Controllers;
use \App\Core\Mvc;
use App\Model\Law;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Controllers\BaseController;

class LawController extends BaseController {

    public function __construct(public Mvc $mvc)
    {
      parent::__construct($mvc);
  
      $this->setLayout('default');
    }

    #[RouteAttr('/cookie')]
    public function home(){
        $laws = Law::findAll();
        return view('laws.law',compact('laws'));
    }

}