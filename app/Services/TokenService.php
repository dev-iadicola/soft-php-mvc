<?php

declare(strict_types=1);

namespace App\Services;

use App\Model\Token;
use DateTime;

class TokenService
{
    /**
     * Generate a new token for the given email address.
     *
     * @return Token|null The created token model, or null on failure.
     */
    public static function generate(string $email): ?Token
    {
        $tokenString = bin2hex(random_bytes(100));

        Token::query()->create([
            'email' => $email,
            'token' => $tokenString,
        ]);

        /** @var Token|null */
        return Token::query()->where('token', $tokenString)->first();
    }

    /**
     * Check whether a token is valid (not used and not expired).
     */
    public static function isValid(string $token): bool
    {
        return !static::isBad($token);
    }

    /**
     * Check whether a token is expired.
     */
    public static function isExpired(string $token): bool
    {
        $tokenModel = Token::query()->where('token', $token)->first();

        if (empty($tokenModel)) {
            return true;
        }

        $expiryDate = new DateTime($tokenModel->expiry_date);
        $currentDate = new DateTime();

        return $currentDate > $expiryDate;
    }

    /**
     * Internal check: returns true when token is bad (used or expired).
     */
    private static function isBad(string $token): bool
    {
        $tokenModel = Token::query()->where('token', $token)->first();

        if (empty($tokenModel)) {
            return true;
        }

        if ($tokenModel->used) {
            return true;
        }

        $expiryDate = new DateTime($tokenModel->expiry_date);
        $currentDate = new DateTime();

        return $currentDate > $expiryDate;
    }
}
