<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::table('visitors')
    ->id()
    ->string('ip_address', 45)->notNull()
    ->text('user_agent')->nullable()
    ->string('url', 500)->nullable()
    ->string('session_id', 128)->nullable()
    ->timestamps()
    ->onDrop('visitors');
