<?php

namespace App\Controllers\Admin;

use App\Core\Mvc;
use App\Core\Validator;
use App\Core\Controller;
use App\Model\Curriculum;
use App\Core\Http\Request;
use App\Core\Storage;

class CurriculumManagmentController extends Controller
{
   public function __construct(Mvc $mvc)
   {
      parent::__construct($mvc);

      $this->setLayout('admin');
   }

   public function index()
   {
      $curricula = Curriculum::findAll();
      return view('admin.cv', compact('curricula'));
   }
   public function store(Request $request)
   {
      $data = $request->getPost();
   
      if ($data['img']['error'] !== UPLOAD_ERR_NO_FILE) {
       
         
         $data['img'] = $this->checkPdf($data);
      }
      Curriculum::save($data);

      $this->withSuccess('CV salvato con Successo!');
      return $this->redirectBack();
   }


   public function edit(Request $request, $id)
   {
      $curriculum = Curriculum::find($id);
      $curricula = Curriculum::orderBy('id', 'DESC')->get();
      return view('admin.cv',  compact('curriculum', 'curricula'));
   }

   public function update(Request $request, $id)
   {
      $data = $request->getPost();
      // Validazione Dati
      if ($data['img']['error'] === UPLOAD_ERR_NO_FILE) {
         $this->withError("I think you need rest, I don't know what you put in the file, but it's definitely not a file.");
         return $this->redirectBack();
      }

      // Trova porgetto
      $project = Curriculum::find($id);
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
      

      $curriculum  = Curriculum::find($id);

      

      $isImgDelete = $this->deleteFile($curriculum->img);


      if ($isImgDelete === TRUE) {
         $this->withSuccess('Curriculum ELIMINATO');
         $curriculum->delete();

      } else {
         $this->withError('Curriculum non eliminato correttamente');

      }

      // Feedback Server

      return $this->redirect('/admin/cv');
   }

   public function download(Request $request, $id){
      $cv = Curriculum::find(id: $id);
      $num = $cv->download + 1;
      $cv->update(['download'=> $num]);
      
   }


}
