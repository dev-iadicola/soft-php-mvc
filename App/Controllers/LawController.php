<?php

namespace App\Controllers;
use \App\Core\Mvc;
use App\Model\Law;
use \App\Core\Controller;
use App\Core\Http\Attributes\AttributeRoute;
use App\Core\Http\Request;

class LawController extends Controller {

    public function __construct(public Mvc $mvc)
    {
      parent::__construct($mvc);
  
      $this->setLayout('default');
    }

    #[AttributeRoute('/cookie')]
    public function home(){
        $laws = Law::findAll();
        return view('laws.law',compact('laws'));
    }

}