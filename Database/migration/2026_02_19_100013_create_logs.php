<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::table('logs')
    ->id()
    ->integer('user_id')->unsigned()->notNull()
    ->datetime('last_log')->defaultRaw('CURRENT_TIMESTAMP')
    ->string('indirizzo', 20)->notNull()
    ->string('device', 300)->notNull()
    ->onDrop('logs');
