<?php

declare(strict_types=1);

namespace App\Core\Contract;

interface CommandInterface
{
    public function exe(array $command): void;
}
