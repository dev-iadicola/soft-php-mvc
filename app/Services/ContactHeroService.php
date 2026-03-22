<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Exception\NotFoundException;
use App\Model\ContactHero;

class ContactHeroService
{
    /**
     * @return array<int, ContactHero>
     */
    public static function getAll(string $orderBy = 'id', string $order = 'DESC'): array
    {
        return ContactHero::query()->orderBy($orderBy, $order)->get();
    }

    public static function getLatest(): ?ContactHero
    {
        /** @var ContactHero|null */
        return ContactHero::query()->orderBy('id', 'DESC')->first();
    }

    /**
     * @throws NotFoundException
     */
    public static function findOrFail(int $id): ContactHero
    {
        $hero = ContactHero::query()->find($id);

        if ($hero === null) {
            throw new NotFoundException("Contact hero with id {$id} not found");
        }

        /** @var ContactHero $hero */
        return $hero;
    }

    public static function create(array $data): ContactHero
    {
        /** @var ContactHero */
        return ContactHero::query()->create($data);
    }

    public static function update(int $id, array $data): bool
    {
        return ContactHero::query()->where('id', $id)->update($data);
    }

    public static function delete(int $id): void
    {
        ContactHero::query()->where('id', $id)->delete();
    }
}
