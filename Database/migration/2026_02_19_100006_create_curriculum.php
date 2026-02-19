<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::table('curriculum')
    ->id()
    ->string('title', 50)->notNull()
    ->string('img', 255)->notNull()
    ->integer('download')->default(0)
    ->onDrop('curriculum');
