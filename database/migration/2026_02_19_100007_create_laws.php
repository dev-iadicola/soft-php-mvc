<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::table('laws')
    ->id()
    ->string('title', 255)->nullable()
    ->mediumText('testo')->nullable()
    ->onDrop('laws');
