<?php 
namespace App\Model;

use App\Core\Eloquent\Model;

class Technology extends Model{
    protected string $table = 'technologies';

    protected array $fillable = [
        'id',
        'name',
    ];
}