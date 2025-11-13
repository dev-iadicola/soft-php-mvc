<?php 
namespace App\Model;

use App\Core\DataLayer\Model;

class Technology extends Model{
    protected string $table = 'technologies';

    protected array $fillable = [
        'id',
        'name',
    ];
}