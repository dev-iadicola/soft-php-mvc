<?php

use App\Core\DataLayer\Seeder\Seeder;

return Seeder::table('technology')
    ->row(['name' => 'PHP'])
    ->row(['name' => 'Laravel'])
    ->row(['name' => 'React'])
    ->row(['name' => 'JavaScript'])
    ->row(['name' => 'C#'])
    ->row(['name' => '.NET'])
    ->row(['name' => 'Python'])
    ->row(['name' => 'MySQL'])
    ->row(['name' => 'HTML'])
    ->row(['name' => 'CSS']);
