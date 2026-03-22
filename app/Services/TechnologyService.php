<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Exception\NotFoundException;
use App\Model\Technology;

class TechnologyService
{
    /**
     * @return array<int, Technology>
     */
    public static function getAll(string $orderBy = 'sort_order', string $order = 'ASC'): array
    {
        return Technology::query()->orderBy($orderBy, $order)->get();
    }

    /**
     * @return array<int, Technology>
     */
    public static function getActive(string $orderBy = 'sort_order', string $order = 'ASC'): array
    {
        return Technology::query()->where('is_active', true)->orderBy($orderBy, $order)->get();
    }

    /**
     * @throws NotFoundException
     */
    public static function findOrFail(int $id): Technology
    {
        $technology = Technology::query()->find($id);

        if ($technology === null) {
            throw new NotFoundException("Technology with id {$id} not found");
        }

        /** @var Technology $technology */
        return $technology;
    }

    public static function create(array $data): Technology
    {
        /** @var Technology */
        return Technology::query()->create($data);
    }

    public static function update(int $id, array $data): bool
    {
        return Technology::query()->where('id', $id)->update($data);
    }

    public static function delete(int $id): void
    {
        Technology::query()->where('id', $id)->delete();
    }
}
