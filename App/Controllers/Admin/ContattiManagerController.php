<?php

namespace App\Controllers\Admin;

use App\Core\Mvc;
use App\Model\Contatti;
use App\Core\Http\Request;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Controllers\AdminController;
use App\Core\Controllers\AuthenticationController;

class ContattiManagerController extends AdminController
{

  #[RouteAttr('/contatti')]
  public function index()
  {
    $contatti = Contatti::orderBy('created_at', 'DESC')->get();
    return view('admin.portfolio.messaggi', compact('contatti'));
  }

  #[RouteAttr('contatti/{id}','GET', 'admin.contatti')]
  public function get(Request $request, int $id)
  {
    $contatti = Contatti::orderBy('created_at', 'DESC')->get();
    $contatto = Contatti::find($id);
    return view('admin.portfolio.messaggi', compact('contatti', 'contatto'));
  }

  #[RouteAttr('/contatti-delete/{id}/','DELETE', 'admin.contatti.delete')]
  public function destroy(int $id)
  {
    $info = "Nome: ".$contatto->nome . " Email:" .$contatto->email;

    if($contatto->delete()){
      return response()->back()->withSuccess("Messaggio eliminato: [$info]");
    }
    return response()->back()->withError("Impossibile eliminare il messaggio [$info]");
  }
}
