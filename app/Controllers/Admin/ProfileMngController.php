<?php

declare(strict_types=1);

namespace   App\Controllers\Admin;

use App\Core\Controllers\AdminController;
use App\Services\ArticleService;
use App\Services\SkillService;
use App\Services\ProfileService;
use App\Core\Http\Request;
use App\Core\Http\Attributes\RouteAttr;

class ProfileMngController extends AdminController
{

  #[RouteAttr(path: 'profile', method: 'get', name: 'profile')]
  public function store(Request $request)
  {
    ProfileService::create($request->all());
    return response()->back()->withSuccess('Skills Aggiornate conn Successo!');
  }

  #[RouteAttr(path: 'profile/{id}', method: 'get', name: 'profile.edit')]
  public function edit(Request $request, string $id)
  {
    $profile = ProfileService::findOrFail((int) $id);
    $skills = SkillService::getAll();
    $articles = ArticleService::getAll();
    $profiles = ProfileService::getAll();
    return view('admin.portfolio.home',  compact('profile','skills','articles','profiles'));
  }

  #[RouteAttr(path: 'profile/{id}', method: 'POST', name: 'profile.update')]
  public function update(Request $request, string $id)
  {
    $data = $request->all();

    $data['selected'] = isset($data['selected']) ? 1 : 0;

    ProfileService::update((int) $id, $data);

    return response()->back()->withSuccess('Aggiornamento Eseguito');

  }

  #[RouteAttr(path: '/profile-delete/{id}', method: 'DELETE', name: 'profile.delete')]
  public function destroy(Request $reqq, int $id){

    ProfileService::delete($id);
    return  response()->back()->withSuccess('Skills ELIMINATE');

 }

}
