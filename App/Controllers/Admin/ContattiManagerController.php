<?php

namespace App\Controllers\Admin;

use App\Core\Mvc;
use App\Model\Contatti;
use App\Core\Controller;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Http\Request;

class ContattiManagerController extends AbstractAdminController
{

  #[RouteAttr('/contatti')]
  public function index()
  {
    $contatti = Contatti::orderBy('created_at', 'DESC')->get();
    return view('admin.portfolio.messaggi', compact('contatti'));
  }

  #[RouteAttr('contatti/{id}')]
  public function get(Request $request, $id)
  {
    $contatti = Contatti::orderBy('created_at', 'DESC')->get();
    $contatto = Contatti::find($id);
    return view('admin.portfolio.messaggi', compact('contatti', 'contatto'));
  }

  #[RouteAttr('/contatti-delete/{id}','DELETE')]
  public function destroy(Request $req, Contatti $contatto)
  {
    $info = $contatto->nome . " " .$contatto->email;

    if($contatto->delete()){
      return response()->back()->withSuccess("Messaggio eliminato $info");
    }
    return response()->back()->withError("Impossibile eliminare il messaggio $info");
  }
}
