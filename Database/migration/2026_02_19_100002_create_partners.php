<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::table('partners')
    ->id()
    ->string('name', 30)->notNull()
    ->string('website', 255)->nullable()
    ->onDrop('partners');
