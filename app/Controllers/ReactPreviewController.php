<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Http\Attributes\Get;

class ReactPreviewController extends Controller
{
    #[Get('/react-preview', 'react.preview')]
    public function index(): void
    {
        inertia('Preview/Welcome', [
            'meta' => [
                'title' => 'React Preview',
            ],
            'preview' => [
                'title' => 'Prima pagina Inertia attiva',
                'description' => 'Questa pagina verifica il bootstrap React, il resolver Inertia e il caricamento degli asset buildati via manifest Vite.',
                'highlights' => [
                    'Shared props server-side disponibili nel client React',
                    'Asset CSS/JS caricati dal manifest di build',
                    'Coesistenza con il routing PHP legacy mantenuta',
                ],
                'next_step' => 'Portare la prima pagina reale o shell condivisa nel branch successivo.',
            ],
        ]);
    }

    #[Get('/react-preview/admin', 'react.preview.admin')]
    public function admin(): void
    {
        inertia('Admin/PreviewDashboard', [
            'meta' => [
                'title' => 'Admin React Preview',
            ],
            'preview' => [
                'title' => 'Admin layout React attivo',
                'description' => 'Questa pagina valida la shell admin condivisa con sidebar, topbar, drawer mobile e page actions.',
                'actions' => [
                    ['href' => '/admin/project', 'label' => 'Manage projects'],
                    ['href' => '/admin/home', 'label' => 'New article', 'variant' => 'primary'],
                ],
                'stats' => [
                    ['label' => 'Unread notifications', 'value' => '04'],
                    ['label' => 'Published articles', 'value' => '18'],
                    ['label' => 'Active projects', 'value' => '09'],
                ],
            ],
        ]);
    }
}
