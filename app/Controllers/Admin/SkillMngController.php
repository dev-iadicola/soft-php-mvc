<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controllers\AdminController;
use App\Core\Http\Attributes\Delete;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Attributes\Middleware;
use App\Core\Http\Attributes\Patch;
use App\Core\Http\Attributes\Prefix;
use App\Services\SkillService;
use App\Core\Http\Request;

#[Prefix('/admin')]
#[Middleware('auth')]
class SkillMngController extends AdminController
{


  #[Get('/skill', 'admin.skill')]
  public function store(Request $request): void
  {
    SkillService::create($request->all());

    response()->back()->withSuccess('Skills Aggiornate conn Successo!');
  }
#[Get('skill-edit/{id}', 'admin.skill.edit')]
  public function edit(Request $request, string $id)
  {
    $skill = SkillService::findOrFail((int) $id);
    return view('admin.portfolio.skill', compact('skill', 'skills', 'articles', 'profiles'));
  }
#[Patch('skill-update/{id}', 'admin.skill.update')]
  public function update(Request $request, string $id)
  {
    $data = $request->all();

    SkillService::update((int) $id, $data);

    return response()->back()->withSuccess('Aggiornamento Eseguito');
  }

  #[Delete('skill-delete/{id}')]
  public function destroy(Request $reqq, string $id)
  {

    SkillService::delete((int) $id);

    return response()->back()->withSuccess('Skills Eliminata con Successo!');
  }
}
