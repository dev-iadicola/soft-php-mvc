<?php

namespace App\Controllers\Admin;

use App\Core\Controllers\AuthenticationController;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Http\Request;
use App\Model\Certificate;

class CorsiManagerController extends AuthenticationController
{

   #[RouteAttr('/corsi')]
   public function index()
   {
      $corsi = Certificate::orderBy('certified', 'DESC')->get();

      return view('/admin/portfolio/corsi',  compact('corsi'));
   }

   #[RouteAttr('corsi-edit/{id}', 'GET')]
   public function edit(Request $request, int  $id)
   {
      $corsi = Certificate::orderBy('id', 'DESC')->get();

      $element = Certificate::find($id);


      if (empty($corsi) || empty($element)) {
         $this->withError('Non è presente ciò che cercate!');
         return response()->back();
      }

      return view('/admin/portfolio/corsi', compact('corsi', 'element'));
   }

   #[RouteAttr('/corsi', 'POST')]
   public function store(Request $request)
   {
      Certificate::create($request->all());
      return  redirect()->back()->withSuccess('Certificate creato con successo!');
   }




   #[RouteAttr('corsi-edit/{id}', 'PATCH')]
   public function update(Request $request, $id)
   {
      Certificate::where('id', $id)->update($request->all());
      $this->withSuccess('Corso Aggiornato con successo!');
      return response()->back();
   }

   #[RouteAttr('corso-delete/{id}','DELETE')]
   public function destroy(Request $request, $id)
   {
      $data = $request->all();
      if (!isset($data['_method']) || !$data['_method'] === 'DELETE') {
         return $this->statusCode413();
      }
      $corso = Certificate::where('id', $id);
      $corso->delete();
      $this->withSuccess('Corso ELIMINATO');
      return response()->back();
   }
}
