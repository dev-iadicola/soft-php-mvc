<?php

declare(strict_types=1);

namespace App\Core\Contract;

use App\Core\DataLayer\Schema\SchemaBuilder;

interface ModelSchemaInterface {
    public static function schema(SchemaBuilder $schema): void;
}
