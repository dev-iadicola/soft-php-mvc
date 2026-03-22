<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/utils',
        __DIR__ . '/database',
    ])
    ->withSkip([
        __DIR__ . '/views',
        __DIR__ . '/vendor',
        __DIR__ . '/storage',
    ])
    ->withRules([
        DeclareStrictTypesRector::class,
    ]);
