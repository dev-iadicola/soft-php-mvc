<?php

declare(strict_types=1);

namespace App\Services;

use App\Model\Certificate;
use App\Core\Exception\NotFoundException;

class CertificateService
{
    /**
     * @return array<int, Certificate>
     */
    public static function getAll(string $orderBy = 'certified', string $order = 'DESC'): array
    {
        return Certificate::query()->orderBy($orderBy, $order)->get();
    }

    /**
     * @throws NotFoundException
     */
    public static function findOrFail(int $id): Certificate
    {
        $certificate = Certificate::query()->find($id);

        if ($certificate === null) {
            throw new NotFoundException("Certificate with id {$id} not found");
        }

        /** @var Certificate $certificate */
        return $certificate;
    }

    public static function create(array $data): Certificate
    {
        /** @var Certificate */
        return Certificate::query()->create($data);
    }

    public static function update(int $id, array $data): bool
    {
        return Certificate::query()->where('id', (string) $id)->update($data);
    }

    public static function delete(int $id): void
    {
        Certificate::query()->where('id', (string) $id)->delete();
    }
}
