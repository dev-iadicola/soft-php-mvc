<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::table('contatti')
    ->id()
    ->string('nome', 100)->nullable()
    ->string('email', 100)->nullable()
    ->longText('messaggio')->nullable()
    ->string('typologie', 20)->nullable()
    ->datetime('created_at')->defaultRaw('CURRENT_TIMESTAMP')
    ->onDrop('contatti');
