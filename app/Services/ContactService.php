<?php

declare(strict_types=1);

namespace App\Services;

use App\Model\Contatti;
use App\Core\Exception\ValidationException;
use App\Core\Exception\NotFoundException;
use App\Services\NotificationService;

class ContactService
{
    /**
     * Validate contact form data.
     *
     * @param array<string, mixed> $data
     *
     * @throws ValidationException
     */
    public static function validate(array $data): void
    {
        $errors = [];

        $nome = (string) ($data['nome'] ?? '');
        $email = (string) ($data['email'] ?? '');
        $messaggio = (string) ($data['messaggio'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email non valida.';
        }

        if (strlen($nome) < 2) {
            $errors[] = 'Il nome deve essere di almeno 2 caratteri.';
        }

        if (strlen($nome) >= 100) {
            $errors[] = 'Il nome non può superare i 99 caratteri.';
        }

        if (strlen($messaggio) < 5) {
            $errors[] = 'Il messaggio deve essere di almeno 5 caratteri.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }

    /**
     * Get all contacts ordered by creation date.
     *
     * @return array<int, Contatti>
     */
    public static function getAll(string $order = 'DESC'): array
    {
        /** @var array<int, Contatti> */
        return Contatti::query()->orderBy('created_at', $order)->get();
    }

    /**
     * Find a contact by ID or throw NotFoundException.
     *
     * @throws NotFoundException
     */
    public static function findOrFail(int $id): Contatti
    {
        /** @var Contatti|null $contatto */
        $contatto = Contatti::query()->find($id);

        if ($contatto === null) {
            throw new NotFoundException("Contatto con ID {$id} non trovato.");
        }

        return $contatto;
    }

    /**
     * Create a new contact entry.
     *
     * @param array<string, mixed> $data
     */
    public static function create(array $data): Contatti
    {
        Contatti::query()->create($data);

        /** @var Contatti */
        $contatto = Contatti::query()->orderBy('id', 'DESC')->first();

        NotificationService::create(
            'new_contact',
            'Nuovo messaggio da ' . ($data['nome'] ?? 'Anonimo'),
            substr((string) ($data['messaggio'] ?? ''), 0, 80),
            '/admin/contatti/' . $contatto->id,
        );

        return $contatto;
    }

    public static function countUnread(): int
    {
        return Contatti::query()->where('is_read', 0)->count();
    }

    public static function markAsRead(int $id): void
    {
        Contatti::query()->where('id', $id)->update(['is_read' => 1]);
    }

    public static function toggleRead(int $id): bool
    {
        $contatto = static::findOrFail($id);
        $newState = !$contatto->is_read;
        Contatti::query()->where('id', $id)->update(['is_read' => $newState ? 1 : 0]);

        return $newState;
    }

    /**
     * @return array<int, string>
     */
    public static function getDistinctTypologies(): array
    {
        /** @var array<int, Contatti> $rows */
        $rows = Contatti::query()
            ->select('typologie')
            ->distinct()
            ->orderBy('typologie')
            ->get();

        $typologies = [];
        foreach ($rows as $row) {
            if ($row->typologie !== null && $row->typologie !== '') {
                $typologies[] = $row->typologie;
            }
        }

        return $typologies;
    }

    /**
     * @return array<int, Contatti>
     */
    public static function getByTypologie(string $typologie, string $order = 'DESC'): array
    {
        /** @var array<int, Contatti> */
        return Contatti::query()
            ->where('typologie', $typologie)
            ->orderBy('created_at', $order)
            ->get();
    }

    /**
     * Delete a contact by ID.
     *
     * @throws NotFoundException
     */
    public static function delete(int $id): void
    {
        $contatto = static::findOrFail($id);

        Contatti::query()->where('id', $id)->delete();
    }
}
