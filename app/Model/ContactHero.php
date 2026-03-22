<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\DataLayer\Model;

class ContactHero extends Model
{
    protected string $table = 'contact_hero';
    protected ?int $id = null;
    protected string $badge;
    protected string $title_html;
    protected string $description_html;
    protected string $primary_stat_value;
    protected string $primary_stat_label;
    protected string $secondary_stat_value;
    protected string $secondary_stat_label;
    protected string $technology_stat_label;
    protected ?string $created_at = null;
    protected ?string $updated_at = null;
}
