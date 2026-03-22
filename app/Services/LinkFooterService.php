<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Exception\NotFoundException;
use App\Model\LinkFooter;

class LinkFooterService
{
    /**
     * @return array<int, LinkFooter>
     */
    public static function getAll(string $orderBy = 'sort_order', string $order = 'ASC'): array
    {
        return LinkFooter::query()->orderBy($orderBy, $order)->get();
    }

    /**
     * @throws NotFoundException
     */
    public static function findOrFail(int $id): LinkFooter
    {
        $link = LinkFooter::query()->find($id);

        if ($link === null) {
            throw new NotFoundException("Footer link with id {$id} not found");
        }

        /** @var LinkFooter $link */
        return $link;
    }

    public static function create(array $data): LinkFooter
    {
        /** @var LinkFooter */
        return LinkFooter::query()->create($data);
    }

    public static function update(int $id, array $data): bool
    {
        return LinkFooter::query()->where('id', $id)->update($data);
    }

    public static function delete(int $id): void
    {
        LinkFooter::query()->where('id', $id)->delete();
    }
}
