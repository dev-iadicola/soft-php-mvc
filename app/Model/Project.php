<?php

declare(strict_types=1);

namespace App\Model;

use App\Core\DataLayer\Model;
use App\Core\Traits\Relation;
use App\Model\Partner;
use App\Model\Technology;
use App\Services\ProjectTechnologyService;


class Project extends Model
{
 use Relation;

   protected ?int $id = null;
   protected ?int $technology_id = null;
   protected ?int $partner_id = null;
   protected string $title;
   protected ?string $overview = null;
   protected ?string $description = null;
   protected ?string $link = null;
   protected ?string $img = null;
   protected ?string $website = null;
   protected int $sort_order = 0;
   protected bool $is_active = true;
   protected ?string $created_at = null;

   protected function casts(): array
   {
      return ['is_active' => 'bool'];
   }
   protected ?string $updated_at = null;

   public function partner(): mixed
   {
      return $this->belongsTo(Partner::class,'partner_id');
   }

   public function technology(): mixed
   {
      return $this->belongsTo(Technology::class, 'technology_id');
   }

   /**
    * @return array<int, Technology>
    */
   public function technologies(): array
   {
      if ($this->id === null) {
         return [];
      }

      return ProjectTechnologyService::getByProject((int) $this->id);
   }
}
