<?php

declare(strict_types=1);

namespace App\Core\DataLayer;

class PaginationResult
{
    /**
     * @param array<int, Model> $items
     */
    public function __construct(
        public readonly array $items,
        public readonly int $currentPage,
        public readonly int $totalPages,
        public readonly int $totalItems,
        public readonly int $perPage,
    ) {}

    public function hasPages(): bool
    {
        return $this->totalPages > 1;
    }

    public function hasPrevious(): bool
    {
        return $this->currentPage > 1;
    }

    public function hasNext(): bool
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
     * @return array<int>
     */
    public function pageRange(int $window = 2): array
    {
        $start = max(1, $this->currentPage - $window);
        $end = min($this->totalPages, $this->currentPage + $window);

        return range($start, $end);
    }
}
