<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::table('portfolio')
    ->id()
    ->string('title', 255)->notNull()
    ->text('overview')->nullable()
    ->string('link', 255)->nullable()
    ->bool('deploy')->default(0)
    ->onDrop('portfolio');
