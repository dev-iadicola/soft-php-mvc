<?php 
namespace App\Model;

use App\Core\Eloquent\Model;

class Partner extends Model
{

    protected string $table = 'partners';

    protected array $fillable = [
        'id',
        'name',
        'website'
    ];
}