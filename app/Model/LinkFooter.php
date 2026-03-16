<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\DataLayer\Model;

class LinkFooter extends Model
{
    protected string $table = 'links_footer';
    protected ?int $id = null;
    protected string $title;
    protected string $link;
    protected ?string $created_at = null;
    protected ?string $updated_at = null;
}
