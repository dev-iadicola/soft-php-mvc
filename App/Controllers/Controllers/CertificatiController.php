<?php 
namespace App\Controllers;
use App\Core\Mvc;
use App\Core\Controller;
use App\Model\Certificate;

class CertificatiController extends Controller{
    
    public function __construct(public Mvc $mvc) {
        parent::__construct($mvc);
    }

    public function index(){
        
        $certificati = (new Certificate())->findAll();

        return $this->render('corsi',[],compact('certificati'));

    }
}