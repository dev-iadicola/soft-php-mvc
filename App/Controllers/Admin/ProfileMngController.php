<?php

namespace   App\Controllers\Admin;

use App\Core\Controllers\AdminController;
use App\Model\Skill;
use App\Model\Article;
use App\Model\Profile;
use App\Core\Controllers\AuthenticationController;
use App\Core\Http\Request;
use App\Core\Http\Attributes\RouteAttr;

class ProfileMngController extends AdminController 
{

  #[RouteAttr(path: 'profile', method: 'get', name: 'profile')]
  public function store(Request $request)
  {   
    Profile::create($request->all());
    return response()->back()->withSuccess('Skills Aggiornate conn Successo!');
  }

  #[RouteAttr(path: 'profile/{id}', method: 'get', name: 'profile.edit')]
  public function edit(Request $request, $id)
  {
    $profile = Profile::find($id);
    $skills = Skill::orderBy('id', 'DESC')->get();
    $articles = Article::orderBy('created_at', 'DESC')->get();
    $profiles = Profile::orderBy('id', 'DESC')->get();
    return view('admin.portfolio.home',  compact('profile','skills','articles','profiles'));
  }

  #[RouteAttr(path: 'profile/{id}', method: 'POST', name: 'profile.update')]
  public function update(Request $request, $id)
  {
    $data = $request->all();

    $data['selected'] = isset($data['selected']) ? 1 : 0;

    $project = Profile::find($id);
    $project->update($data);

    return response()->back()->withSuccess('Aggiornamento Eseguito');
    
  }

  #[RouteAttr(path: '/profile-delete/{id}', method: 'DELETE', name: 'profile.delete')]
  public function destroy(Request $reqq, int $id){
    // trova e azione
    $data =  $reqq->all();

    $project  = Profile::find($id);
    $project->delete();
    return  response()->back()->withSuccess('Skills ELIMINATE');

 }

}
