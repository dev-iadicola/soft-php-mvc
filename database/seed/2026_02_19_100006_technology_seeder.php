<?php

use App\Core\DataLayer\Seeder\Seeder;

return Seeder::table('technology')
    ->row(['name' => 'PHP'])
    ->row(['name' => 'Laravel'])
    ->row(['name' => 'Filament'])
    ->row(['name' => 'Java'])
    ->row(['name' => 'Spring Boot'])
    ->row(['name' => 'React'])
    ->row(['name' => 'TypeScript'])
    ->row(['name' => 'JavaScript'])
    ->row(['name' => 'C#'])
    ->row(['name' => '.NET'])
    ->row(['name' => 'Python'])
    ->row(['name' => 'PostgreSQL'])
    ->row(['name' => 'MySQL'])
    ->row(['name' => 'Docker'])
    ->row(['name' => 'REST API'])
    ->row(['name' => 'HTML'])
    ->row(['name' => 'CSS']);
