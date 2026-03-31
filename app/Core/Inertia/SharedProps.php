<?php

declare(strict_types=1);

namespace App\Core\Inertia;

use Throwable;

class SharedProps
{
    /** @var array<string, mixed> */
    private static array $shared = [];

    public static function share(string|array $key, mixed $value = null): void
    {
        if (is_array($key)) {
            self::$shared = self::merge(self::$shared, $key);
            return;
        }

        self::$shared[$key] = $value;
    }

    /**
     * @return array<string, mixed>
     */
    public static function resolve(array $pageProps = []): array
    {
        $resolvedShared = [];

        foreach (self::$shared as $key => $value) {
            $resolvedShared[$key] = is_callable($value) ? $value() : $value;
        }

        return self::merge(self::defaultProps(), $resolvedShared, $pageProps);
    }

    public static function reset(): void
    {
        self::$shared = [];
    }

    /**
     * @return array<string, mixed>
     */
    private static function defaultProps(): array
    {
        return [
            'app' => [
                'name' => 'Soft MVC',
                'csrf_token' => self::csrfToken(),
            ],
            'auth' => [
                'user' => self::authUser(),
            ],
            'flash' => [
                'success' => self::flash('success'),
                'warning' => self::flash('warning'),
                'error' => self::flash('error'),
            ],
        ];
    }

    /**
     * @param array<string, mixed> ...$arrays
     * @return array<string, mixed>
     */
    private static function merge(array ...$arrays): array
    {
        $merged = [];

        foreach ($arrays as $array) {
            $merged = array_replace_recursive($merged, $array);
        }

        return $merged;
    }

    private static function csrfToken(): ?string
    {
        try {
            if (mvc()?->sessionStorage === null) {
                return null;
            }

            return function_exists('csrf_token') ? csrf_token() : null;
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function authUser(): ?array
    {
        if (!function_exists('auth')) {
            return null;
        }

        try {
            $user = auth()->user();
            if (!is_object($user)) {
                return null;
            }

            return [
                'id' => $user->id ?? null,
                'email' => $user->email ?? null,
            ];
        } catch (Throwable) {
            return null;
        }
    }

    private static function flash(string $key): mixed
    {
        try {
            if (!function_exists('session') || mvc()?->sessionStorage === null) {
                return null;
            }

            return session()->getFlash($key);
        } catch (Throwable) {
            return null;
        }
    }
}
