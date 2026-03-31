<?php

declare(strict_types=1);

namespace App\Core\Inertia;

use App\Core\Helpers\Seo;
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
        $seo = self::seoDefaults();

        return [
            'app' => [
                'name' => self::appName(),
                'csrf_token' => self::csrfToken(),
                'url' => Seo::baseUrl(),
            ],
            'auth' => [
                'user' => self::authUser(),
            ],
            'flash' => [
                'success' => self::flash('success'),
                'warning' => self::flash('warning'),
                'error' => self::flash('error'),
            ],
            'site' => [
                'base_url' => Seo::baseUrl(),
                'maintenance_page' => self::maintenancePage(),
            ],
            'navigation' => [
                'main' => self::mainNavigation(),
            ],
            'routing' => [
                'current' => self::currentPath(),
                'canonical' => $seo['url'],
            ],
            'seo' => [
                'title' => $seo['title'],
                'description' => $seo['description'],
                'canonical' => $seo['url'],
                'image' => $seo['image'],
            ],
            'meta' => [
                'title' => $seo['title'],
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

    private static function appName(): string
    {
        return 'Iadicola // dev';
    }

    /**
     * @return array<int, array{href: string, label: string, external: bool}>
     */
    private static function mainNavigation(): array
    {
        $configured = mvc()?->config?->get('menu') ?? [];

        if (!is_array($configured) || $configured === []) {
            return self::fallbackNavigation();
        }

        $navigation = [];

        foreach ($configured as $href => $label) {
            if (!is_string($href) || !is_string($label)) {
                continue;
            }

            $external = str_starts_with($href, 'http://') || str_starts_with($href, 'https://');
            $normalizedHref = $external ? $href : self::normalizePath($href);

            $navigation[] = [
                'href' => $normalizedHref,
                'label' => $label,
                'external' => $external,
            ];
        }

        return $navigation;
    }

    /**
     * @return array<int, array{href: string, label: string, external: bool}>
     */
    private static function fallbackNavigation(): array
    {
        return [
            ['href' => '/', 'label' => 'Home', 'external' => false],
            ['href' => '/portfolio', 'label' => 'Portfolio', 'external' => false],
            ['href' => '/progetti', 'label' => 'Progetti', 'external' => false],
            ['href' => '/blog', 'label' => 'Blog', 'external' => false],
            ['href' => '/contatti', 'label' => 'Contatti', 'external' => false],
        ];
    }

    private static function currentPath(): string
    {
        return self::normalizePath(mvc()?->request?->uri() ?? ($_SERVER['REQUEST_URI'] ?? '/'));
    }

    private static function maintenancePage(): ?string
    {
        $page = mvc()?->config?->get('settings.pages.MAINTENANCE');

        return is_string($page) && $page !== '' ? $page : null;
    }

    /**
     * @return array{title: string, description: string, image: string, url: string}
     */
    private static function seoDefaults(): array
    {
        return Seo::make();
    }

    private static function normalizePath(string $path): string
    {
        if ($path === '') {
            return '/';
        }

        if ($path[0] !== '/') {
            $path = '/' . $path;
        }

        return $path === '/' ? $path : rtrim($path, '/');
    }
}
