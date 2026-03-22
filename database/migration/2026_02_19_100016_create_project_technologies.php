<?php

use App\Core\DataLayer\Migration\Migration;

return Migration::table('project_technologies')
    ->integer('project_id')->unsigned()->notNull()
    ->integer('technology_id')->unsigned()->notNull()
    ->primaryComposite(['project_id', 'technology_id'])
    ->foreignKey('project_id', 'projects', 'id', 'CASCADE')
    ->foreignKey('technology_id', 'technology', 'id', 'CASCADE')
    ->onDrop('project_technologies');
