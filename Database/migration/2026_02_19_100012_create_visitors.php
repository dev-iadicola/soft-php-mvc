<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::table('visitors')
    ->id()
    ->string('ip', 45)->notNull()
    ->string('user_agent', 200)->nullable()
    ->uniqueComposite(['ip', 'user_agent'])
    ->onDrop('visitors');
