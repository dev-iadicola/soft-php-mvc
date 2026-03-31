<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controllers\AdminController;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Attributes\Middleware;
use App\Core\Http\Attributes\Post;
use App\Core\Http\Attributes\Prefix;
use App\Core\Http\Request;
use App\Services\EmailTemplateService;

#[Prefix('/admin')]
#[Middleware('auth')]
class EmailTemplateController extends AdminController
{
    #[Get('/email-templates', 'admin.emailTemplates')]
    public function index()
    {
        $templates = EmailTemplateService::getAll();
        return view('admin.portfolio.email-templates', compact('templates'));
    }

    #[Get('/email-templates/{id}/edit', 'admin.emailTemplates.edit')]
    public function edit(int $id)
    {
        $templates = EmailTemplateService::getAll();
        $template = EmailTemplateService::findOrFail($id);
        return view('admin.portfolio.email-templates', compact('templates', 'template'));
    }

    #[Post('/email-templates/{id}', 'admin.emailTemplates.update')]
    public function update(Request $request, int $id)
    {
        $data = [
            'subject' => trim((string) $request->get('subject')),
            'body' => (string) $request->get('body'),
            'is_active' => $request->get('is_active') !== null ? 1 : 0,
        ];

        if ($data['subject'] === '') {
            return response()->back()->withError('L\'oggetto non può essere vuoto.');
        }

        EmailTemplateService::update($id, $data);

        return response()->back()->withSuccess('Template aggiornato con successo.');
    }
}
