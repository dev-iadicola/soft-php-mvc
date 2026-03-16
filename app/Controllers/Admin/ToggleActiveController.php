<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controllers\AdminController;
use App\Core\Http\Attributes\Middleware;
use App\Core\Http\Attributes\Patch;
use App\Core\Http\Attributes\Prefix;
use App\Core\Http\Request;
use App\Model\Article;
use App\Model\Certificate;
use App\Model\Partner;
use App\Model\Project;
use App\Model\Skill;
use App\Model\Technology;

#[Prefix('/admin')]
#[Middleware('auth')]
class ToggleActiveController extends AdminController
{
    private const ENTITY_MAP = [
        'project' => Project::class,
        'technology' => Technology::class,
        'partner' => Partner::class,
        'skill' => Skill::class,
        'article' => Article::class,
        'certificate' => Certificate::class,
    ];

    #[Patch('/toggle-active', 'admin.toggle-active')]
    public function toggle(Request $request)
    {
        $entity = $request->string('entity');
        $id = $request->integer('id');

        if (!isset(self::ENTITY_MAP[$entity])) {
            return response()->json(['error' => 'Unknown entity.'], 400);
        }

        if ($id <= 0) {
            return response()->json(['error' => 'Invalid id.'], 400);
        }

        /** @var class-string<\App\Core\DataLayer\Model> $modelClass */
        $modelClass = self::ENTITY_MAP[$entity];

        $record = $modelClass::query()->find($id);

        if ($record === null) {
            return response()->json(['error' => 'Record not found.'], 404);
        }

        $newState = !((bool) $record->is_active);

        $modelClass::query()->where('id', $id)->update([
            'is_active' => $newState ? 1 : 0,
        ]);

        return response()->json(['success' => true, 'is_active' => $newState]);
    }
}
