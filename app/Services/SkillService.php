<?php

declare(strict_types=1);

namespace App\Services;

use App\Model\Skill;
use App\Core\Exception\NotFoundException;

class SkillService
{
    /**
     * @return array<int, Skill>
     */
    public static function getAll(string $orderBy = 'id', string $order = 'DESC'): array
    {
        return Skill::query()->orderBy($orderBy, $order)->get();
    }

    /**
     * @return array<int, Skill>
     */
    public static function getActive(string $orderBy = 'id', string $order = 'DESC'): array
    {
        return Skill::query()->where('is_active', true)->orderBy($orderBy, $order)->get();
    }

    /**
     * @throws NotFoundException
     */
    public static function findOrFail(int $id): Skill
    {
        $skill = Skill::query()->find($id);

        if ($skill === null) {
            throw new NotFoundException("Skill with id {$id} not found");
        }

        /** @var Skill $skill */
        return $skill;
    }

    public static function create(array $data): Skill
    {
        /** @var Skill */
        return Skill::query()->create($data);
    }

    public static function update(int $id, array $data): bool
    {
        return Skill::query()->where('id', $id)->update($data);
    }

    public static function delete(int $id): void
    {
        Skill::query()->where('id', $id)->delete();
    }
}
