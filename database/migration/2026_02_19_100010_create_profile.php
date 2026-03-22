<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::table('profile')
    ->id()
    ->string('name', 100)->notNull()
    ->string('tagline', 255)->nullable()
    ->text('welcome_message')->nullable()
    ->bool('selected')->default(1)
    ->onDrop('profile');
