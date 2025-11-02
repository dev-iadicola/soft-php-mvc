<?php

namespace   App\Controllers\Admin;




use App\Core\Mvc;
use App\Model\Skill;
use App\Model\Profile;
use App\Core\Controller;
use App\Core\Http\Request;
use App\Model\Article;

class ProfileMngController extends AbstractAdminController 
{

  public function store(Request $request)
  {   
    Profile::create($request->all());
    return response()->back()->withSuccess('Skills Aggiornate conn Successo!');
  }

  public function edit(Request $request, $id)
  {
    $profile = Profile::find($id);
    $skills = Skill::orderBy('id', 'DESC')->get();
    $articles = Article::orderBy('created_at', 'DESC')->get();
    $profiles = Profile::orderBy('id', 'DESC')->get();
    return view('admin.portfolio.home',  compact('profile','skills','articles','profiles'));
  }

  public function update(Request $request, $id)
  {
    $data = $request->all();

    $data['selected'] = isset($data['selected']) ? 1 : 0;


    $project = Profile::find($id);
    $project->update($data);

    return response()->back()->withSuccess('Aggiornamento Eseguito');
    
  }

  public function destroy(Request $reqq, $id){
    // trova e azione
   $data =  $reqq->all();
   if( !isset($data['_method']) ||!$data['_method'] === 'DELETE'){
    return $this->statusCode413();
   }

    $project  = Skill::find($id);
    $project->delete();
    return  response()->back()->withSuccess('Skills ELIMINATE');

 }

}
