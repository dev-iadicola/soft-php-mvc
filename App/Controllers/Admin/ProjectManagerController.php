<?php

namespace App\Controllers\Admin;

use App\Core\Mvc;
use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\Storage;
use App\Core\Validator;
use App\Model\Project;

class ProjectManagerController extends AbstractAdminController
{


   public function index()
   {
      $projects = Project::orderBy('id', 'DESC')->get();
      return view('admin.portfolio.project',  compact('projects'));
   }

   public function store(Request $request)
   {
      $projects = Project::orderBy('id', 'DESC')->get();
      $data = $request->all();

      // Controlla se è stato caricato un file immagine
      if (isset($data['img']) && is_array($data['img']) && $data['img']['error'] !== UPLOAD_ERR_NO_FILE) {

         $file = $data['img'];

         // Verifica che sia un'immagine valida
         if (!Validator::verifyImage($file)) {
            // Gestisci errore (es. ritorna con errore, throw, ecc)
            return response()->back()->withError('Il file caricato non è un\'immagine valida.');
         }
         // Salva il file e ottieni il path relativo
         $storage = new Storage();
         $storage->disk('images')->put($file);

         // Sovrascrivi $data['img'] con il path relativo (da salvare nel DB)
         $data['img'] = $storage->getRelativePath();
      }

      // Crea il progetto
      Project::create($data);

      $this->withSuccess('Progetto salvato con Successo!');
      return view('admin.portfolio.project', compact('projects'));
   }

   public function edit(Request $request, $id)
   {

      $project = Project::find($id);
      $projects = Project::orderBy('id', 'DESC')->get();
      return view('admin.portfolio.project', compact('project', 'projects'));
   }

   public function update(Request $request, $id)
   {
      $data = $request->all();
      $project = Project::find($id);
      // Validazione Dati
      if ($data['img']['error'] === UPLOAD_ERR_NO_FILE) {
         $this->withWarning("Aggiornato, eccetto l'immagine");
      }
      if ($data['img']['error'] !== UPLOAD_ERR_NO_FILE) {
         $stg = new Storage();
         $stg->deleteIfFileExist($project->img);
         $stg->disk('images')->put($data['img']);
         $data['img'] = $stg->getRelativePath();
         $this->withSuccess('Aggiornamento Eseguito');
      } else {
         unset($data['img']);
      }
      // Trova porgetto
      $project = Project::find($id);
      $project->update($data);
      // feedback server

      response()->back()->withSuccess('Progetto aggiornato con successo!');
   }

   public function destroy(Request $reqq, $id)
   {
      // trova e azione
      $data =  $reqq->all();
      if (!isset($data['_method']) || !$data['_method'] === 'DELETE') {
         return $this->statusCode413();
      }

      $project  = Project::find($id);
      if (!$project) {
         return response()->back()->withError('Progetto non trovato');
      }
      if (!isset($project->img) && !is_null($project->img)) {
         $stg = new Storage();
         if ($stg->deleteIfFileExist($project->img)) {
            $project->delete();
            response()->back()->withSuccess('Progetto Elimianto.');
         } else {

            response()->back()->withWarning('Non è stato possibile eliminare il progetto, perchè manca il percorso dell\'immagine.');
         }

         
      }

      response()->back()->withError("Progetto non eliminato correttamente.");
   }
}
