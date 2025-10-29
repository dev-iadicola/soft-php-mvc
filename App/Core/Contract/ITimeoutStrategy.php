<?php
namespace App\Core\Contract;

interface ITimeoutStrategy
{
    public function IsExpired():bool;
}

