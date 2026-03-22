<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::table('technology')
    ->id()
    ->string('name', 30)->notNull()
    ->onDrop('technology');
