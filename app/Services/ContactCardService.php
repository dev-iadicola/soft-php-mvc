<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Exception\NotFoundException;
use App\Model\ContactCard;

class ContactCardService
{
    /**
     * @return array<int, ContactCard>
     */
    public static function getAll(string $orderBy = 'sort_order', string $order = 'ASC'): array
    {
        return ContactCard::query()->orderBy($orderBy, $order)->get();
    }

    /**
     * @throws NotFoundException
     */
    public static function findOrFail(int $id): ContactCard
    {
        $card = ContactCard::query()->find($id);

        if ($card === null) {
            throw new NotFoundException("Contact card with id {$id} not found");
        }

        /** @var ContactCard $card */
        return $card;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function create(array $data): ContactCard
    {
        /** @var ContactCard */
        return ContactCard::query()->create($data);
    }

    public static function update(int $id, array $data): bool
    {
        return ContactCard::query()->where('id', $id)->update($data);
    }

    public static function delete(int $id): void
    {
        ContactCard::query()->where('id', $id)->delete();
    }
}
