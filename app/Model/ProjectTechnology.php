<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\DataLayer\Model;

class ProjectTechnology extends Model
{
    protected ?int $project_id = null;
    protected ?int $technology_id = null;
    protected ?string $created_at = null;
    protected ?string $updated_at = null;
}
