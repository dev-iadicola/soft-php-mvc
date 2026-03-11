<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Http\Request;
use App\Model\Certificate;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Controllers\AdminController;
use App\Core\Controllers\AuthenticationController;

class CorsiManagerController extends AdminController
{

   #[RouteAttr('/corsi')]
   public function index()
   {
      $corsi = Certificate::query()->orderBy('certified', 'DESC')->get();

      return view('/admin/portfolio/corsi',  compact('corsi'));
   }

   #[RouteAttr('corsi-edit/{id}', 'GET')]
   public function edit(Request $request, int  $id)
   {
      $corsi = Certificate::query()->orderBy('id', 'DESC')->get();

      $element = Certificate::query()->find($id);


      if (empty($corsi) || empty($element)) {
         $this->withError('Non è presente ciò che cercate!');
         return response()->back();
      }

      return view('/admin/portfolio/corsi', compact('corsi', 'element'));
   }

   #[RouteAttr('/corsi', 'POST')]
   public function store(Request $request)
   {
      Certificate::query()->create($request->all());
      return  redirect()->back()->withSuccess('Certificate creato con successo!');
   }




   #[RouteAttr('corsi-edit/{id}', 'PATCH')]
   public function update(Request $request, string $id)
   {
      Certificate::query()->where('id', $id)->update($request->all());
      $this->withSuccess('Corso Aggiornato con successo!');
      return response()->back();
   }

   #[RouteAttr('corso-delete/{id}','DELETE')]
   public function destroy(Request $request, string $id)
   {
      $data = $request->all();
      if (!isset($data['_method']) || !$data['_method'] === 'DELETE') {
         return $this->statusCode413();
      }
      Certificate::query()->where('id', $id)->delete();
      $this->withSuccess('Corso ELIMINATO');
      return response()->back();
   }
}
