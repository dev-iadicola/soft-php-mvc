<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Exception\ValidationException;
use App\Model\User;

class FirstUserSetupService
{
    public static function requiresRegistration(): bool
    {
        return User::query()->count() === 0;
    }

    /**
     * @throws ValidationException
     */
    public static function ensureRegistrationOpen(): void
    {
        if (!self::requiresRegistration()) {
            throw new ValidationException(
                ['registration' => 'Registrazione non disponibile: esiste gia un account admin.'],
                'Registrazione non disponibile.'
            );
        }
    }

    /**
     * @throws ValidationException
     */
    public static function createInitialUser(string $email, string $passwordHash): User
    {
        self::ensureRegistrationOpen();

        /** @var User $user */
        $user = User::query()->create([
            'email' => $email,
            'password' => $passwordHash,
        ]);

        return $user;
    }
}
