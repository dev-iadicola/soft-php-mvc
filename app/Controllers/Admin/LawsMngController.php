<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controllers\AdminController;
use App\Model\Law;
use App\Core\Controllers\AuthenticationController;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Http\Request;

class LawsMngController extends AdminController{

    #[RouteAttr('/laws')]  
    public function index(){

        $laws=  Law::query()->orderBy('id', 'DESC')->get();
         return view('admin.laws.index',compact('laws'));
        }
       
       
        #[RouteAttr('/laws', 'POST')]  
        public function store(Request $request){        
         Law::query()->create( $request->all());
        
         $this->withSuccess('New Law has be created');
         return response()->back();
        }
        #[RouteAttr(path: 'laws/{id}', method: 'get', name: 'laws.edit')]
        public function edit(Request $req, string $id){
         $law = Law::query()->find($id);

         $laws = Law::query()->all();
        
         return view( 'admin.laws.index' , compact('laws','law')  );
        
        }

        #[RouteAttr(path: 'laws/{id}', method: 'patch', name: 'laws.update')]
        public function update(Request $request, string $id){
          Law::query()->where('id', $id)->update($request->all());
          
          $this->withSuccess('Law is Updated');
          return response()->back();
        }
       
        #[RouteAttr(path: 'laws-delete/{id}', method: 'DELETE', name: 'laws.delete')]
        public function destroy(Request $req, string $id){
          $data =  $req->all();
       
          $law = Law::query()->where('id', $id)->delete();

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
