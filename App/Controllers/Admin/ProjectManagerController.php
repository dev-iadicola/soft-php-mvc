<?php

namespace App\Controllers\Admin;

use App\Core\Mvc;
use App\Core\Storage;
use App\Model\Project;
use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Validation\Validator;

class ProjectManagerController extends AbstractAdminController
{

   #[RouteAttr(path: 'project', method: 'get', name: 'project')]
   public function index()
   {
      $projects = Project::orderBy('id', 'DESC')->get();
      return view('admin.portfolio.project',  compact('projects'));
   }


   #[RouteAttr(path: 'project/{id}', method: 'get', name: 'project')]
   public function edit(Request $request, $id)
   {

      $project = Project::find($id);
      $projects = Project::orderBy('id', 'DESC')->get();
      return view('admin.portfolio.project', compact('project', 'projects'));
   }


   #[RouteAttr(path: 'project', method: 'POST', name: 'project')]
   public function store(Request $request)
   {

      $data = $request->all();

      // Controlla se è stato caricato un file immagine
      if (isset($data['img']) && is_array($data['img']) && $data['img']['error'] !== UPLOAD_ERR_NO_FILE) {

         $file = $data['img'];
         $validator = Validator::make($request->all(), ['img' => ['required', 'image']], ['img' => "Il file caricato non è un'immagine valida."]);
         // Verifica che sia un'immagine valida
         if ($validator->fails()) {
            // Gestisci errore (es. ritorna con errore, throw, ecc)
            return response()->back()->withError($validator->errors());
         }
         // Salva il file e ottieni il path relativo
         $storage = new Storage();
         $storage->disk('images')->put($file);

         // Sovrascrivi $data['img'] con il path relativo (da salvare nel DB)
         $data['img'] = $storage->getRelativePath();
      }

      // Crea il progetto
      Project::create($data);
      return redirect()->back()->withSuccess("Progetto salvato con Successo!");
   }


   #[RouteAttr(path: 'project/{id}', method: 'POST', name: 'project')]
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

     return response()->back()->withSuccess('Progetto aggiornato con successo!');
   }

   #[RouteAttr(path: 'project-delete/{id}', method: 'DELETE', name: 'project')]
   public function destroy(Request $reqq, $id)
   {
      // trova e azione
      $data =  $reqq->all();
      

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
