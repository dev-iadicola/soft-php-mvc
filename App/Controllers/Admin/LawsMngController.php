<?php
namespace App\Controllers\Admin;

use App\Core\Mvc;
use App\Model\Law;
use App\Core\Controller;
use App\Core\Http\Request;

class LawsMngController extends AbstractAdminController{

  
    public function index(){

        $laws=  Law::orderBy('id', 'DESC')->get();
         return view('admin.laws.index',compact('laws'));
        }
        
        public function store(Request $request){        
         Law::create( $request->all());
        
         $this->withSuccess('New Law has be created');
         return response()->back();
        }
        
        public function edit(Request $req, $id){
         $law = Law::find($id);
        
         $laws = Law::findAll();
        
         return view( 'admin.laws.index' , compact('laws','law')  );
        
        }

        public function update(Request $request, $id){
          $law = Law::find($id);

          $law->update($request->all());
          
          $this->withSuccess('Law is Updated');
          return response()->back();
        }
       
        public function destroy(Request $req, $id){
          $data =  $req->all();
         if( !isset($data['_method']) ||!$data['_method'] === 'DELETE'){
          return $this->statusCode413();
         }
          $law = Law::find(id: $id)->delete();

          if($law ===  true){
           return  response()->back()->withSuccess("Law DELETE");
          }
          if($law === null){
            return response()->back();
          }
          
         
            $this->withError('LAW NOT WAS DELETED!');
            return response()->back();
        }
        
}
