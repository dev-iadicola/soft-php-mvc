<?php

declare(strict_types=1);

namespace App\Core\Helpers;

class Paginator
{
    private int $currentPage;
    private int $perPage;
    private int $totalItems;
    private int $totalPages;

    /** @var array<int, mixed> */
    private array $items;

    /**
     * @param array<int, mixed> $items
     */
    public function __construct(array $items, int $totalItems, int $currentPage, int $perPage)
    {
        $this->items = $items;
        $this->totalItems = $totalItems;
        $this->perPage = $perPage;
        $this->currentPage = max(1, $currentPage);
        $this->totalPages = (int) ceil($totalItems / max(1, $perPage));
    }

    /** @return array<int, mixed> */
    public function items(): array
    {
        return $this->items;
    }

    public function currentPage(): int
    {
        return $this->currentPage;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function totalItems(): int
    {
        return $this->totalItems;
    }

    public function totalPages(): int
    {
        return $this->totalPages;
    }

    public function hasPages(): bool
    {
        return $this->totalPages > 1;
    }

    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->totalPages;
    }

    public function previousPage(): int
    {
        return max(1, $this->currentPage - 1);
    }

    public function nextPage(): int
    {
        return min($this->totalPages, $this->currentPage + 1);
    }

    /**
     * Build a URL for a given page, preserving existing query string params.
     */
    public function url(int $page, string $baseUrl = ''): string
    {
        if ($baseUrl === '') {
            $baseUrl = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
        }

        $params = $_GET;
        $params['page'] = $page;

        return $baseUrl . '?' . http_build_query($params);
    }

    /**
     * Generate an array of page numbers for rendering (with gaps as null).
     *
     * @return array<int|null>
     */
    public function pageRange(int $window = 2): array
    {
        if ($this->totalPages <= 1) {
            return [1];
        }

        $pages = [];
        $from = max(1, $this->currentPage - $window);
        $to = min($this->totalPages, $this->currentPage + $window);

        if ($from > 1) {
            $pages[] = 1;
            if ($from > 2) {
                $pages[] = null; // gap
            }
        }

        for ($i = $from; $i <= $to; $i++) {
            $pages[] = $i;
        }

        if ($to < $this->totalPages) {
            if ($to < $this->totalPages - 1) {
                $pages[] = null; // gap
            }
            $pages[] = $this->totalPages;
        }

        return $pages;
    }
}
