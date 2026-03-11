<?php

declare(strict_types=1);

namespace App\Services;

use App\Model\Contatti;
use App\Core\Exception\ValidationException;
use App\Core\Exception\NotFoundException;

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
        return Contatti::query()->orderBy('id', 'DESC')->first();
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
