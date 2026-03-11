<?php

declare(strict_types=1);

namespace App\Core\Contract;

interface ITimeoutStrategy
{
    public function IsExpired():bool;
}
