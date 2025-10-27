<?php

namespace App\Controllers\Admin;

use App\Core\Mvc;
use App\Core\Controller;
use App\Core\Http\Request;
use App\Model\Certificate;

class CorsiManagerController extends Controller
{

   public function __construct(public Mvc $mvc)
   {
      parent::__construct($mvc);

      $this->setLayout('admin');
   }

   public function index()
   {
      $corsi = Certificate::orderBy('certified', 'DESC')->get();

      return view('/admin/portfolio/corsi',  compact('corsi'));
   }

   public function store(Request $request)
   {

      Certificate::create($request->getPost());

      return  $this->redirectBack()->withSuccess('Certificate Inserito');
   }


   public function edit(Request $request, $id)
   {
      $corsi = Certificate::orderBy('id', 'DESC')->get();

      $element = Certificate::find($id);


      if (empty($corsi) || empty($element)) {
         $this->withError('Non è presente ciò che cercate!');
         return $this->redirectBack();
      }

      return view('/admin/portfolio/corsi', compact('corsi', 'element'));
   }

   public function update(Request $request, $id)
   {
      Certificate::where('id', $id)->update($request->getPost());
      $this->withSuccess('Corso Aggiornato con successo!');
      return $this->redirectBack();
   }
   public function destroy(Request $request, $id)
   {
      $data = $request->getPost();
      if (!isset($data['_method']) || !$data['_method'] === 'DELETE') {
         return $this->statusCode413();
      }

      $corso = Certificate::where('id', $id);
      $corso->delete();
      $this->withSuccess('Corso ELIMINATO');
      return $this->redirectBack();
   }
}
