<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::table('links_footer')
    ->id()
    ->string('title', 50)->notNull()
    ->string('link', 255)->notNull()
    ->onDrop('links_footer');
