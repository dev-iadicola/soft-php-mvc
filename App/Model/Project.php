<?php

namespace App\Model;

use App\Core\DataLayer\Model;
use App\Core\Traits\Relation;
use App\Model\Partner;


class Project extends Model
{
 use Relation;
   /**
    * Summary of table
    * @var string $table 
    * Questa variabile è importante per poter inserire staticamente il nome della colonna 
    * permettendoci di rispamiare tempo
    * 
    */
   protected string $table = 'projects';

   protected bool $timestamps = false;
   protected int|string|null $technology_id = null;
   protected int|string|null $partner_id = null;
   protected ?string $title = null;
   protected ?string $overview = null;
   protected ?string $description = null;
   protected ?string $link = null;
   protected ?string $img = null;
   protected ?string $website = null;

   public function partner()
   {
      return $this->belongsTo(Partner::class,'partner_id');
   }

   public function technology()
   {
      return $this->hasMany(Technology::class,'technology_id');
   }
}
