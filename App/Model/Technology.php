<?php 
namespace App\Model;

use App\Core\Eloquent\ORM;

class Technology extends ORM{
    protected string $table = 'technologies';

    protected array $fillable = [
        'id',
        'name',
    ];
}