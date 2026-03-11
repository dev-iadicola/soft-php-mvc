<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::table('tokens')
    ->stringId('token', 255)
    ->string('email', 60)->nullable()
    ->bool('used')->default(0)
    ->datetime('created_at')->defaultRaw('CURRENT_TIMESTAMP')
    ->raw("`expiry_date` DATETIME GENERATED ALWAYS AS (`created_at` + INTERVAL 5 MINUTE) STORED")
    ->foreignKey('email', 'users', 'email', '', 'CASCADE')
    ->onDrop('tokens');
