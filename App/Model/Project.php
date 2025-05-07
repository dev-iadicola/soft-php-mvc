<?php

namespace App\Model;

use App\Core\Eloquent\Model;
use App\Model\Partner;
use App\Traits\Getter;
use App\Traits\Relation;

class Project extends Model
{
   use Getter; use Relation;

   /**
    * Summary of table
    * @var string $table 
    * Questa variabile è importante per poter inserire staticamente il nome della colonna 
    * permettendoci di rispamiare tempo
    * 
    */
   protected string $table = 'projects';
   protected array $fillable = [
      'technology_id',
      'partner_id',
      'title',
      'overview',
      'description',
      'link',
      'img',
      'website'
   ];

   public function partner()
   {
      return $this->belongsTo(Partner::class,'partner_id');
   }

   public function technology()
   {
      return $this->hasMany(Technology::class,'technology_id');
   }
}
