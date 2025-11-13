<?php

namespace App\Model;

use App\Core\DataLayer\Model;
use App\Model\Partner;


class Project extends Model
{


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
