<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::table('skills')
    ->id()
    ->string('title', 100)->notNull()
    ->text('description')->nullable()
    ->onDrop('skills');
