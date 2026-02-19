<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::table('articles')
    ->id()
    ->string('title', 50)->notNull()
    ->string('subtitle', 200)->notNull()
    ->text('overview')->nullable()
    ->string('img', 1000)->nullable()
    ->string('link', 1000)->nullable()
    ->datetime('created_at')->defaultRaw('CURRENT_TIMESTAMP')
    ->onDrop('articles');
