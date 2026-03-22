<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\DataLayer\Model;

class ContactCard extends Model
{
    protected string $table = 'contact_cards';
    protected ?int $id = null;
    protected string $icon;
    protected string $color;
    protected string $title;
    protected string $description;
    protected string $tags;
    protected int $sort_order = 0;
    protected ?string $created_at = null;
    protected ?string $updated_at = null;

    /**
     * @return array<int, string>
     */
    public function getTagsArray(): array
    {
        return array_map('trim', explode(',', $this->tags));
    }
}
