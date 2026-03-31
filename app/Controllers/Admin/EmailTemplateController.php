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
        try {
            $templates = EmailTemplateService::getAll();
        } catch (\Throwable) {
            $templates = [];
        }

        return inertia('Admin/EmailTemplates', [
            'meta' => [
                'title' => 'Template email',
            ],
            'emailTemplatesPage' => [
                'current' => null,
                'placeholders' => [
                    ['label' => '{nome}', 'description' => 'Nome del mittente'],
                    ['label' => '{email}', 'description' => 'Email del mittente'],
                    ['label' => '{messaggio}', 'description' => 'Messaggio originale'],
                ],
                'templates' => array_map([$this, 'serializeTemplate'], $templates),
            ],
        ]);
    }

    #[Get('/email-templates/{id}/edit', 'admin.emailTemplates.edit')]
    public function edit(int $id)
    {
        $templates = EmailTemplateService::getAll();
        $template = EmailTemplateService::findOrFail($id);

        return inertia('Admin/EmailTemplates', [
            'meta' => [
                'title' => 'Template email',
            ],
            'emailTemplatesPage' => [
                'current' => $this->serializeTemplate($template),
                'placeholders' => [
                    ['label' => '{nome}', 'description' => 'Nome del mittente'],
                    ['label' => '{email}', 'description' => 'Email del mittente'],
                    ['label' => '{messaggio}', 'description' => 'Messaggio originale'],
                ],
                'templates' => array_map([$this, 'serializeTemplate'], $templates),
            ],
        ]);
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

    /**
     * @return array{
     *   id: int,
     *   slug: string,
     *   subject: string,
     *   body: string,
     *   isActive: bool,
     *   updatedAt: string
     * }
     */
    private function serializeTemplate(object $template): array
    {
        return [
            'id' => (int) ($template->id ?? 0),
            'slug' => (string) ($template->slug ?? ''),
            'subject' => (string) ($template->subject ?? ''),
            'body' => (string) ($template->body ?? ''),
            'isActive' => (bool) ($template->is_active ?? false),
            'updatedAt' => (string) ($template->updated_at ?? ''),
        ];
    }
}
