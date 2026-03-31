<?php

declare(strict_types=1);

namespace App\Services;

use App\Model\AuthSession;

class AuthSessionService
{
    public static function create(string $id, int $userId, string $ip, ?string $userAgent = null): AuthSession
    {
        /** @var AuthSession|null $existing */
        $existing = AuthSession::query()->where('id', $id)->first();

        $payload = [
            'user_id' => $userId,
            'ip' => $ip,
            'user_agent' => $userAgent,
            'last_activity' => date('Y-m-d H:i:s'),
        ];

        if ($existing instanceof AuthSession) {
            AuthSession::query()->where('id', $id)->update($payload);
        } else {
            AuthSession::query()->create($payload + ['id' => $id]);
        }

        /** @var AuthSession */
        return AuthSession::query()->where('id', $id)->first();
    }

    public static function touch(string $id): void
    {
        AuthSession::query()->where('id', $id)->update([
            'last_activity' => date('Y-m-d H:i:s'),
        ]);
    }

    public static function terminate(string $id): void
    {
        AuthSession::query()->where('id', $id)->delete();
    }

    /**
     * @return array<int, AuthSession>
     */
    public static function getForUser(int $userId): array
    {
        return AuthSession::query()
            ->where('user_id', $userId)
            ->orderBy('last_activity', 'DESC')
            ->get();
    }

    public static function find(string $id): ?AuthSession
    {
        /** @var AuthSession|null */
        return AuthSession::query()->where('id', $id)->first();
    }
}
