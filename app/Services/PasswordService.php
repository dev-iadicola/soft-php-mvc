<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Exception\ValidationException;
use App\Model\User;

class PasswordService
{
    /**
     * Change the password for a user identified by email.
     *
     * @return bool True if the password was changed, false if user not found.
     */
    public static function changeByEmail(string $email, string $newPassword): bool
    {
        $user = User::query()->where('email', $email)->first();

        if (empty($user)) {
            return false;
        }

        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        User::query()->where('email', $email)->update(['password' => $hashed]);

        return true;
    }

    /**
     * Change the password for an authenticated user with validation.
     *
     * @throws ValidationException If current password is wrong, passwords don't match, or too short.
     */
    public static function changeByUser(
        User $user,
        string $currentPassword,
        string $newPassword,
        string $confirmed
    ): void {
        if (!password_verify($currentPassword, $user->password)) {
            throw new ValidationException(
                ['current_password' => 'La password attuale non e corretta.'],
                'La password attuale non e corretta.'
            );
        }

        if (strlen($newPassword) < 8) {
            throw new ValidationException(
                ['password' => 'La nuova password deve contenere almeno 8 caratteri.'],
                'La nuova password deve contenere almeno 8 caratteri.'
            );
        }

        if ($newPassword !== $confirmed) {
            throw new ValidationException(
                ['confirmed' => 'Le password non coincidono.'],
                'Le password non coincidono.'
            );
        }

        User::query()->where('id', $user->id)->update([
            'password' => password_hash($newPassword, PASSWORD_BCRYPT),
        ]);
    }
}
