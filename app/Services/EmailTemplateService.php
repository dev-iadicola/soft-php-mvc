<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Exception\NotFoundException;
use App\Model\EmailTemplate;

class EmailTemplateService
{
    /**
     * @return array<int, EmailTemplate>
     */
    public static function getAll(): array
    {
        return EmailTemplate::query()->orderBy('slug')->get();
    }

    public static function findBySlug(string $slug): ?EmailTemplate
    {
        /** @var EmailTemplate|null */
        return EmailTemplate::query()->where('slug', $slug)->first();
    }

    public static function findOrFail(int $id): EmailTemplate
    {
        /** @var EmailTemplate|null $template */
        $template = EmailTemplate::query()->find($id);

        if ($template === null) {
            throw new NotFoundException("EmailTemplate with id {$id} not found");
        }

        return $template;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function update(int $id, array $data): bool
    {
        return EmailTemplate::query()->where('id', $id)->update($data);
    }

    /**
     * Replace placeholders in template body.
     *
     * @param array<string, string> $variables e.g. ['nome' => 'Mario', 'email' => 'mario@test.com']
     */
    public static function render(string $body, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $body = str_replace('{' . $key . '}', $value, $body);
        }

        return $body;
    }

    /**
     * Send an auto-reply using a template, if active.
     *
     * @param array<string, string> $variables
     */
    public static function sendIfActive(string $slug, string $toEmail, array $variables): bool
    {
        $template = self::findBySlug($slug);

        if ($template === null || !$template->is_active) {
            return false;
        }

        $subject = self::render($template->subject, $variables);
        $body = self::render($template->body, $variables);

        try {
            $mailer = new \App\Mail\BrevoMail();
            $mailer->bodyHtml = $body;
            $mailer->setEmail($toEmail, $subject);
            $mailer->send();

            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
