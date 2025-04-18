<?php 
namespace App\Model;

use App\Core\Eloquent\ORM;
use App\Traits\Getter;
use App\Traits\Relation;

class Partner extends ORM
{
    use Getter; use Relation;
    protected string $table = 'partners';

    protected array $fillable = [
        'id',
        'name',
        'website'
    ];
}