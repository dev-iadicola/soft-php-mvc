<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::table('contact_cards')
    ->id()
    ->string('icon', 50)->notNull()
    ->string('color', 20)->notNull()
    ->string('title', 150)->notNull()
    ->text('description')->notNull()
    ->string('tags', 255)->notNull()
    ->integer('sort_order')->default(0)
    ->timestamps()
    ->onDrop('contact_cards');
