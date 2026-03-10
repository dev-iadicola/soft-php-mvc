<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::table('projects')
    ->id()
    ->integer('technology_id')->unsigned()->nullable()
    ->integer('partner_id')->unsigned()->nullable()
    ->string('title', 100)->notNull()
    ->string('overview', 500)->nullable()
    ->text('description')->nullable()
    ->string('link', 255)->nullable()
    ->string('img', 255)->nullable()
    ->string('website', 255)->nullable()
    ->foreignKey('technology_id', 'technology', 'id', 'SET NULL')
    ->foreignKey('partner_id', 'partners', 'id', 'SET NULL')
    ->onDrop('projects');
