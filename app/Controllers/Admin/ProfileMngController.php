<?php

declare(strict_types=1);

namespace   App\Controllers\Admin;

use App\Core\Controllers\AdminController;
use App\Core\Http\Attributes\Delete;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Attributes\Middleware;
use App\Core\Http\Attributes\Prefix;
use App\Core\Http\Attributes\Post;
use App\Core\Facade\Storage;
use App\Core\Helpers\ImageHelper;
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
  public function edit(int $id)
  {
    $profile = ProfileService::findOrFail($id);
    $skills = SkillService::getAll();
    $articles = ArticleService::getAll();
    $profiles = ProfileService::getAll();
    return view('admin.portfolio.home',  compact('profile','skills','articles','profiles'));
  }

  #[Post('profile/{id}', 'profile.update')]
  public function update(Request $request, int $id)
  {
    $data = $request->all();
    $data['selected'] = isset($data['selected']) ? 1 : 0;

    if ($request->hasFile('avatar')) {
        $file = $request->file('avatar');
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = 'avatar_' . $id . '.' . $extension;
        $path = 'profile/' . $filename;

        $content = file_get_contents($file['tmp_name']);
        $content = ImageHelper::processFromString($content, $extension, 200, 200);

        $storage = Storage::make('public');
        $storage->put($path, $content, ['visibility' => 'public']);
        $data['avatar'] = $storage->getPath($path);
    } else {
        unset($data['avatar']);
    }

    ProfileService::update($id, $data);

    return response()->back()->withSuccess('Aggiornamento Eseguito');
  }

  #[Delete('/profile-delete/{id}', 'profile.delete')]
  public function destroy(int $id){

    ProfileService::delete($id);
    return  response()->back()->withSuccess('Skills ELIMINATE');

 }

}
