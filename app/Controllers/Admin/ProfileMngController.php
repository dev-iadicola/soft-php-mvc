<?php

declare(strict_types=1);

namespace   App\Controllers\Admin;

use App\Core\Controllers\AdminController;
use App\Core\Http\Attributes\Delete;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Attributes\Middleware;
use App\Core\Http\Attributes\Prefix;
use App\Core\Http\Attributes\Post;
use App\Services\ArticleService;
use App\Services\SkillService;
use App\Services\ProfileService;
use App\Core\Http\Request;

#[Prefix('/admin')]
#[Middleware('auth')]
class ProfileMngController extends AdminController
{

  #[Get('profile', 'profile')]
  public function store(Request $request)
  {
    ProfileService::create($request->all());
    return response()->back()->withSuccess('Skills Aggiornate conn Successo!');
  }

  #[Get('profile/{id}', 'profile.edit')]
  public function edit(Request $request, string $id)
  {
    $profile = ProfileService::findOrFail((int) $id);
    $skills = SkillService::getAll();
    $articles = ArticleService::getAll();
    $profiles = ProfileService::getAll();
    return view('admin.portfolio.home',  compact('profile','skills','articles','profiles'));
  }

  #[Post('profile/{id}', 'profile.update')]
  public function update(Request $request, string $id)
  {
    $data = $request->all();

    $data['selected'] = isset($data['selected']) ? 1 : 0;

    ProfileService::update((int) $id, $data);

    return response()->back()->withSuccess('Aggiornamento Eseguito');

  }

  #[Delete('/profile-delete/{id}', 'profile.delete')]
  public function destroy(Request $reqq, int $id){

    ProfileService::delete($id);
    return  response()->back()->withSuccess('Skills ELIMINATE');

 }

}
