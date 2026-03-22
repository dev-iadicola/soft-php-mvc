<?php

declare(strict_types=1);

namespace App\Services;

use App\Model\Law;
use App\Core\Exception\NotFoundException;

class LawService
{
    /**
     * @return array<int, Law>
     */
    public static function getAll(string $orderBy = 'id', string $order = 'DESC'): array
    {
        return Law::query()->orderBy($orderBy, $order)->get();
    }

    /**
     * @throws NotFoundException
     */
    public static function findOrFail(int $id): Law
    {
        $law = Law::query()->find($id);

        if ($law === null) {
            throw new NotFoundException("Law with id {$id} not found");
        }

        /** @var Law $law */
        return $law;
    }

    public static function create(array $data): Law
    {
        /** @var Law */
        return Law::query()->create($data);
    }

    public static function update(int $id, array $data): bool
    {
        return Law::query()->where('id', (string) $id)->update($data);
    }

    public static function delete(int $id): void
    {
        Law::query()->where('id', (string) $id)->delete();
    }
}
