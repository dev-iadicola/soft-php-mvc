<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::table('contact_hero')
    ->id()
    ->string('badge', 100)->notNull()
    ->text('title_html')->notNull()
    ->text('description_html')->notNull()
    ->string('primary_stat_value', 50)->notNull()
    ->string('primary_stat_label', 100)->notNull()
    ->string('secondary_stat_value', 50)->notNull()
    ->string('secondary_stat_label', 100)->notNull()
    ->string('technology_stat_label', 100)->notNull()
    ->timestamps()
    ->onDrop('contact_hero');
