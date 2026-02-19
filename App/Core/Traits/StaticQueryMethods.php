<?php

namespace App\Core\Traits;

use App\Core\DataLayer\Query\ActiveQuery;
use App\Core\DataLayer\Factory\ActiveQueryFactory;

trait StaticQueryMethods
{
    public static function query(): ActiveQuery
    {
        return ActiveQueryFactory::for(static::class);
    }
}
