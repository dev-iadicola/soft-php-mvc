<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controllers\AdminController;
use App\Core\Http\Attributes\Middleware;
use App\Core\Http\Attributes\Patch;
use App\Core\Http\Attributes\Prefix;
use App\Core\Http\Request;
use App\Model\LinkFooter;
use App\Model\Partner;
use App\Model\Project;
use App\Model\Technology;

#[Prefix('/admin')]
#[Middleware('auth')]
class SortOrderController extends AdminController
{
    private const ENTITY_MAP = [
        'project' => Project::class,
        'technology' => Technology::class,
        'partner' => Partner::class,
        'link_footer' => LinkFooter::class,
    ];

    #[Patch('/sort-order', 'admin.sort-order.update')]
    public function update(Request $request)
    {
        $entity = $request->string('entity');
        $order = $request->get('order', []);

        if (!isset(self::ENTITY_MAP[$entity])) {
            return response()->json(['error' => 'Unknown entity.'], 400);
        }

        if (!is_array($order) || $order === []) {
            return response()->json(['error' => 'Order array is required.'], 400);
        }

        /** @var class-string<\App\Core\DataLayer\Model> $modelClass */
        $modelClass = self::ENTITY_MAP[$entity];

        foreach ($order as $position => $id) {
            $modelClass::query()->where('id', (int) $id)->update([
                'sort_order' => $position,
            ]);
        }

        return response()->json(['success' => true]);
    }
}
