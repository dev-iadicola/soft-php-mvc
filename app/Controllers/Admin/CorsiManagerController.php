<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Http\Request;
use App\Core\Controllers\AdminController;
use App\Core\Http\Attributes\Delete;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Attributes\Middleware;
use App\Core\Http\Attributes\Patch;
use App\Core\Http\Attributes\Prefix;
use App\Core\Http\Attributes\Post;
use App\Services\CertificateService;

#[Prefix('/admin')]
#[Middleware('auth')]
class CorsiManagerController extends AdminController
{

   #[Get('/corsi')]
   public function index()
   {
      $corsi = CertificateService::getAll();

      return view('/admin/portfolio/corsi',  compact('corsi'));
   }

   #[Get('corsi-edit/{id}')]
   public function edit(Request $request, int  $id)
   {
      $corsi = CertificateService::getAll('id');

      $element = CertificateService::findOrFail($id);

      return view('/admin/portfolio/corsi', compact('corsi', 'element'));
   }

   #[Post('/corsi')]
   public function store(Request $request)
   {
      CertificateService::create($request->all());
      return  redirect()->back()->withSuccess('Certificate creato con successo!');
   }

   #[Patch('corsi-edit/{id}')]
   public function update(Request $request, string $id)
   {
      CertificateService::update((int) $id, $request->all());
      return response()->back()->withSuccess('Corso Aggiornato con successo!');
   }

   #[Delete('corso-delete/{id}')]
   public function destroy(Request $request, string $id)
   {
      $data = $request->all();
      if (!isset($data['_method']) || !$data['_method'] === 'DELETE') {
         return response()->set413();
      }
      CertificateService::delete((int) $id);
      return response()->back()->withSuccess('Corso ELIMINATO');
   }
}
