<?php
namespace App\Controllers\Admin;

use App\Core\Mvc;
use App\Model\Law;
use App\Core\Controller;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Http\Request;

class LawsMngController extends AbstractAdminController{

    #[RouteAttr('/laws')]  
    public function index(){

        $laws=  Law::orderBy('id', 'DESC')->get();
         return view('admin.laws.index',compact('laws'));
        }
       
       
        #[RouteAttr('/laws', 'POST')]  
        public function store(Request $request){        
         Law::create( $request->all());
        
         $this->withSuccess('New Law has be created');
         return response()->back();
        }
        #[RouteAttr(path: 'laws/{id}', method: 'get', name: 'laws.edit')]
        public function edit(Request $req, $id){
         $law = Law::find($id);
        
         $laws = Law::findAll();
        
         return view( 'admin.laws.index' , compact('laws','law')  );
        
        }

        #[RouteAttr(path: 'laws/{id}', method: 'patch', name: 'laws.update')]
        public function update(Request $request, $id){
          $law = Law::find($id);

          $law->update($request->all());
          
          $this->withSuccess('Law is Updated');
          return response()->back();
        }
       
        #[RouteAttr(path: 'laws-delete/{id}', method: 'DELETE', name: 'laws.delete')]
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
