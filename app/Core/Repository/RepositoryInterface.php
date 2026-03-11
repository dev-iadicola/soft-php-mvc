<?php

declare(strict_types=1);

namespace App\Core\Repository;

use App\Core\DataLayer\Model;

interface RepositoryInterface
{
    public function find(int|string $id): ?Model;

    public function all(): array;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Model;

    /**
     * @param array<string, mixed> $data
     */
    public function update(int|string $id, array $data): bool;

    public function delete(int|string $id): bool;
}
