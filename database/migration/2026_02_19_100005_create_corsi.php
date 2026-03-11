<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::table('corsi')
    ->id()
    ->string('title', 50)->notNull()
    ->text('overview')->nullable()
    ->string('link', 1000)->notNull()
    ->year('certified')->notNull()
    ->string('ente', 100)->notNull()
    ->onDrop('corsi');
