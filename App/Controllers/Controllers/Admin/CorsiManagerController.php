<?php
namespace App\Controllers\Admin;

use App\Core\Mvc;
use App\Core\Controller;
use App\Core\Http\Request;
use App\Model\Certificato;

 class CorsiManagerController extends Controller{
     
    public function __construct(public Mvc $mvc)
    {
       parent::__construct($mvc);
 
       $this->setLayout('admin');
    }
 
    public function index(){
        $corsi = Certificato::orderBy('id DESC')->get();

       return $this->render('/admin/portfolio/corsi',[],compact('corsi'));

    }

    public function store(Request $request){

      var_dump($request->getPost()); 
      Certificato::save($request->getPost());

     return  $this->redirectBack()->withSuccess('Certificato Inserito');
        
    }


    public function edit(Request $request, $id){
      $corsi = Certificato::orderBy('id DESC')->get();

      $element = Certificato::find($id);
      

      if(empty($corsi) || empty($element)){
          $this->withError('Non è presente ciò che cercate!');
         return $this->redirectBack();

      }

      return $this->render('/admin/portfolio/corsi',[],compact('corsi','element'));

  }

  public function update(Request $request, $id){

   Certificato::where('id',$id)->update($request->getPost());
   $this->withSuccess('Corso Aggiornato con successo!');
   return $this->redirectBack() ;
  }
   public function destroy(Request $request, $id){
      $data = $request->getPost();
      if( !isset($data['_method']) ||!$data['_method'] === 'DELETE'){
         return $this->statusCode413();
        }
        
     $corso = Certificato::where('id',$id);
     $corso->delete();
$this->withSuccess('Corso ELIMINATO');
      return $this->redirectBack();
   }
 }