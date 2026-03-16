<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Exception\NotFoundException;
use App\Model\Partner;

class PartnerService
{
    /**
     * @return array<int, Partner>
     */
    public static function getAll(string $orderBy = 'name', string $order = 'ASC'): array
    {
        return Partner::query()->orderBy($orderBy, $order)->get();
    }

    /**
     * @throws NotFoundException
     */
    public static function findOrFail(int $id): Partner
    {
        $partner = Partner::query()->find($id);

        if ($partner === null) {
            throw new NotFoundException("Partner with id {$id} not found");
        }

        /** @var Partner $partner */
        return $partner;
    }

    public static function create(array $data): Partner
    {
        /** @var Partner */
        return Partner::query()->create($data);
    }

    public static function update(int $id, array $data): bool
    {
        return Partner::query()->where('id', $id)->update($data);
    }

    public static function delete(int $id): void
    {
        Partner::query()->where('id', $id)->delete();
    }
}
