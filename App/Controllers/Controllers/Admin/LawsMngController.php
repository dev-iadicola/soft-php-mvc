<?php
namespace App\Controllers\Admin;

use App\Core\Mvc;
use App\Model\Law;
use App\Core\Controller;
use App\Core\Http\Request;

class LawsMngController extends Controller{

    public function __construct(public Mvc $mvc)
    {
      parent::__construct($mvc);
  
      $this->setLayout('admin');
    }
  
    public function index(){

        $laws=  Law::orderBy('id desc')->get();
         return $this->render('admin.laws.index',[],compact('laws'));
        }
        
        public function store(Request $request){

        
         Law::dirtySave( $request->getPost());
        
         $this->withSuccess('New Law has be created');
         return $this->redirectBack();
        }
        
        public function edit(Request $req, $id){
         $law = Law::find($id);
        
         $laws = Law::findAll();
        
         return $this->render( 'admin.laws.index' , [], compact('laws','law')  );
        
        }

        public function update(Request $request, $id){
          $law = Law::find($id);

          $law->dirtyUpdate($request->getPost());
          
          $this->withSuccess('Law is Updated');
          return $this->redirectBack();
        }
       
        public function destroy(Request $req, $id){
          $data =  $req->getPost();
         if( !isset($data['_method']) ||!$data['_method'] === 'DELETE'){
          return $this->statusCode413();
         }
          $law = Law::find(id: $id)->delete();

          if($law ===  true){
            $this->withSuccess('Law DELETE');
           return  $this->redirectBack();
          }
          if($law === null){
            return $this->redirectBack();
          }
         
            $this->withError('LAW NOT WAS DELETED!');
            return $this->redirectBack();
          

        }
        
}
