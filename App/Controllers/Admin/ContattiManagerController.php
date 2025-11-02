<?php 
namespace App\Controllers\Admin;

use App\Core\Mvc;
use App\Model\Contatti;
use App\Core\Controller;
use App\Core\Http\Request;

class ContattiManagerController extends AbstractAdminController{
  
    public function index(){
       $contatti = Contatti::orderBy('created_at', 'DESC')->get();
       return view('admin.portfolio.messaggi', compact('contatti'));
    }

    public function get(Request $request, $id){
      $contatti = Contatti::orderBy('created_at', 'DESC')->get();
      $contatto = Contatti::find($id);
      return view('admin.portfolio.messaggi',compact('contatti','contatto'));

    }

    public function destroy(Request $req, $id){
      $data =  $req->all();
      if( !isset($data['_method']) ||!$data['_method'] === 'DELETE'){
       return $this->statusCode413();
      }
    

    $contatto = Contatti::find($id);

    $contatto->delete();

    $this->withSuccess('Messaggio ELIMINATO');
   return $this->redirect();
   }

}