<?php

namespace   App\Controllers\Admin;




use App\Core\Mvc;
use App\Model\Skill;
use App\Model\Profile;
use App\Core\Controller;
use App\Core\Http\Request;
use App\Model\Article;

class ProfileMngController extends Controller 
{

  public function __construct(public Mvc $mvc)
  {
    parent::__construct($mvc);

    $this->setLayout('admin');
  }


  public function store(Request $request)
  {   
    Profile::save($request->getPost());

    $this->redirectBack()->withSuccess('Skills Aggiornate conn Successo!');
  }

  public function edit(Request $request, $id)
  {
    $profile = Profile::find($id);
    $skills = Skill::orderBy('id DESC')->get();
    $articles = Article::orderBy('created_at DESC')->get();
    $profiles = Profile::orderBy('id desc')->get();
    $this->render('admin.portfolio.home', [], compact('profile','skills','articles','profiles'));
  }

  public function update(Request $request, $id)
  {
    $data = $request->getPost();

    $data['selected'] = isset($data['selected']) ? 1 : 0;


    $project = Profile::find($id);
    $project->update($data);

    $this->withSuccess('Aggiornamento Eseguito');
    $this->redirectBack();
  }

  public function destroy(Request $reqq, $id){
    // trova e azione
   $data =  $reqq->getPost();
   if( !isset($data['_method']) ||!$data['_method'] === 'DELETE'){
    return $this->statusCode413();
   }

    $project  = Skill::find($id);

    $project->delete();
// Feedback Server
    return $this->redirectBack()->withSuccess('Skills ELIMINATE');

 }

}
