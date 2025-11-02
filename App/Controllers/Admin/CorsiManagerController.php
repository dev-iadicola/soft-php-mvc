<?php

namespace App\Controllers\Admin;

use App\Core\Mvc;
use App\Core\Controller;
use App\Core\Http\Request;
use App\Model\Certificate;

class CorsiManagerController extends AbstractAdminController
{



   public function index()
   {
      $corsi = Certificate::orderBy('certified', 'DESC')->get();

      return view('/admin/portfolio/corsi',  compact('corsi'));
   }

   public function store(Request $request)
   {

      Certificate::create($request->all());

      return  redirect()->back()->withSuccess('Certificate Inserito');
   }


   public function edit(Request $request, $id)
   {
      $corsi = Certificate::orderBy('id', 'DESC')->get();

      $element = Certificate::find($id);


      if (empty($corsi) || empty($element)) {
         $this->withError('Non è presente ciò che cercate!');
         return response()->back();
      }

      return view('/admin/portfolio/corsi', compact('corsi', 'element'));
   }

   public function update(Request $request, $id)
   {
      Certificate::where('id', $id)->update($request->getPost());
      $this->withSuccess('Corso Aggiornato con successo!');
      return response()->back();
   }
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
