<?php

namespace App\Controllers\Admin;

use App\Core\Mvc;
use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\Storage;
use App\Core\Validator;
use App\Model\Project;

class ProjectManagerController extends Controller
{
   public function __construct(public Mvc $mvc)
   {
      parent::__construct($mvc);

      $this->setLayout('admin');
   }

   public function index()
   {
      $projects = Project::orderBy('id DESC')->get();
      return view('admin.portfolio.project',  compact('projects'));
   }

   public function store(Request $request)
   {
      $projects = Project::orderBy('id DESC')->get();
      $data = $request->getPost();

      if ($data['img']['error'] !== UPLOAD_ERR_NO_FILE) {
         $data['img'] = $this->checkImage($data);
      }


      Project::update($data);

      $this->withSuccess('Progetto salvato con Successo!');
      return $this->redirectBack();
   }

   public function edit(Request $request, $id)
   {

      $project = Project::find($id);
      $projects = Project::orderBy('id DESC')->get();
      return view('admin.portfolio.project', compact('project', 'projects'));
   }

   public function update(Request $request, $id)
   {
      $data = $request->getPost();
      $project = Project::find($id);
      // Validazione Dati
      if ($data['img']['error'] === UPLOAD_ERR_NO_FILE) {
         $this->withError("Aggiornato, eccetto l'immagine");
         // return $this->redirectBack();
      }
      if ($data['img']['error'] !== UPLOAD_ERR_NO_FILE) {

         $this->deleteFile($project->img);

         $data['img'] = $this->checkImage($data);

      } else {
         unset($data['img']);
      }

      // Trova porgetto
      $project = Project::find($id);
      $project->update($data);


      // feedback server
      $this->withSuccess('Aggiornamento Eseguito');
      $this->redirectBack();
   }

   public function destroy(Request $reqq, $id)
   {
      // trova e azione
      $data =  $reqq->getPost();
      if (!isset($data['_method']) || !$data['_method'] === 'DELETE') {
         return $this->statusCode413();
      }

      $project  = Project::find($id);
      if (!isset($project->img)) {
         $project->delete();
         $this->withSuccess('Progetto ELIMINATO, non è stata trovata alcuna immagine');
         return $this->redirect('/admin/progetti');
      }

      if ($this->deleteFile($project->img) === TRUE) {
         $this->withSuccess('Progetto ELIMINATO');
         $project->delete();
         return $this->redirect('/admin/progetti');
      }

      $this->withError('Progetto non eliminato correttamente');
      return $this->redirect('/admin/progetti');
   }


}
