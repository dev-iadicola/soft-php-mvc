<?php

declare(strict_types=1);

namespace App\Services;

use App\Model\Profile;
use App\Core\Exception\NotFoundException;

class ProfileService
{
    /**
     * @return array<int, Profile>
     */
    public static function getAll(string $orderBy = 'id', string $order = 'DESC'): array
    {
        return Profile::query()->orderBy($orderBy, $order)->get();
    }

    /**
     * @return array<int, Profile>
     */
    public static function getSelected(): array
    {
        return Profile::query()->where('selected', true)->orderBy('id', 'DESC')->get();
    }

    /**
     * @throws NotFoundException
     */
    public static function findOrFail(int $id): Profile
    {
        $profile = Profile::query()->find($id);

        if ($profile === null) {
            throw new NotFoundException("Profile with id {$id} not found");
        }

        /** @var Profile $profile */
        return $profile;
    }

    public static function create(array $data): Profile
    {
        /** @var Profile */
        return Profile::query()->create($data);
    }

    public static function update(int $id, array $data): bool
    {
        return Profile::query()->where('id', $id)->update($data);
    }

    public static function delete(int $id): void
    {
        Profile::query()->where('id', $id)->delete();
    }
}
