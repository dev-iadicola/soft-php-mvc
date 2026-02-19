<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::table('users')
    ->id()
    ->integer('log_id')->unsigned()->nullable()
    ->string('password', 255)->notNull()
    ->string('email', 60)->notNull()->unique()
    ->string('token', 255)->nullable()->unique()
    ->datetime('created_at')->defaultRaw('CURRENT_TIMESTAMP')
    ->onDrop('users');
