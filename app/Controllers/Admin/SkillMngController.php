<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controllers\AdminController;
use App\Core\Http\Attributes\RouteAttr;
use App\Services\SkillService;
use App\Core\Http\Request;

class SkillMngController extends AdminController
{


  #[RouteAttr('/skill', 'get', 'admin.skill')]
  public function store(Request $request): void
  {
    SkillService::create($request->all());

    response()->back()->withSuccess('Skills Aggiornate conn Successo!');
  }
#[RouteAttr('skill-edit/{id}', 'get','admin.skill.edit')]
  public function edit(Request $request, string $id)
  {
    $skill = SkillService::findOrFail((int) $id);
    return view('admin.portfolio.skill', compact('skill', 'skills', 'articles', 'profiles'));
  }
#[RouteAttr('skill-update/{id}', 'patch','admin.skill.update')]
  public function update(Request $request, string $id)
  {
    $data = $request->all();

    SkillService::update((int) $id, $data);

    return response()->back()->withSuccess('Aggiornamento Eseguito');
  }

  #[RouteAttr('skill-delete/{id}', 'delete')]
  public function destroy(Request $reqq, string $id)
  {

    SkillService::delete((int) $id);

    return response()->back()->withSuccess('Skills Eliminata con Successo!');
  }
}
