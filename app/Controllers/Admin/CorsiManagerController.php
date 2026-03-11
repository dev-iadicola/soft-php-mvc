<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Http\Request;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Controllers\AdminController;
use App\Services\CertificateService;

class CorsiManagerController extends AdminController
{

   #[RouteAttr('/corsi')]
   public function index()
   {
      $corsi = CertificateService::getAll();

      return view('/admin/portfolio/corsi',  compact('corsi'));
   }

   #[RouteAttr('corsi-edit/{id}', 'GET')]
   public function edit(Request $request, int  $id)
   {
      $corsi = CertificateService::getAll('id');

      $element = CertificateService::findOrFail($id);

      return view('/admin/portfolio/corsi', compact('corsi', 'element'));
   }

   #[RouteAttr('/corsi', 'POST')]
   public function store(Request $request)
   {
      CertificateService::create($request->all());
      return  redirect()->back()->withSuccess('Certificate creato con successo!');
   }

   #[RouteAttr('corsi-edit/{id}', 'PATCH')]
   public function update(Request $request, string $id)
   {
      CertificateService::update((int) $id, $request->all());
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
      CertificateService::delete((int) $id);
      $this->withSuccess('Corso ELIMINATO');
      return response()->back();
   }
}
