<?php

namespace App\Controllers\Admin;

use App\Core\Controllers\AdminController;
use App\Core\Controllers\AuthenticationController;
use App\Core\Http\Attributes\RouteAttr;
use App\Model\Skill;
use App\Core\Http\Request;

class SkillMngController extends AdminController
{


  #[RouteAttr('/skill', 'get', 'admin.skill')]
  public function store(Request $request)
  {
    Skill::Make()->create($request->all());
    
    response()->back()->withSuccess('Skills Aggiornate conn Successo!');
  }
#[RouteAttr('skill-edit/{id}', 'get','admin.skill.edit')]
  public function edit(Request $request, $id)
  {
    $skill = Skill::find($id);
    return view('admin.portfolio.skill', compact('skill', 'skills', 'articles', 'profiles'));
  }
#[RouteAttr('skill-update/{id}', 'patch','admin.skill.update')]
  public function update(Request $request, $id)
  {
    $data = $request->all();

    $project = Skill::find($id);
    $project->update($data);

    return response()->back()->withSuccess('Aggiornamento Eseguito');
  }

  #[RouteAttr('skill-delete/{id}', 'delete')]
  public function destroy(Request $reqq, $id)
  {

    $project  = Skill::find($id);

    $project->delete();

    return response()->back()->withSuccess('Skills Eliminata con Successo!');
  }
}
