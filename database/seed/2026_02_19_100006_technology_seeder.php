<?php

use App\Core\DataLayer\Seeder\Seeder;

return Seeder::table('technology')
    ->row(['name' => 'PHP', 'sort_order' => 1])
    ->row(['name' => 'Laravel', 'sort_order' => 2])
    ->row(['name' => 'Filament', 'sort_order' => 3])
    ->row(['name' => 'Java', 'sort_order' => 4])
    ->row(['name' => 'Spring Boot', 'sort_order' => 5])
    ->row(['name' => 'React', 'sort_order' => 6])
    ->row(['name' => 'TypeScript', 'sort_order' => 7])
    ->row(['name' => 'JavaScript', 'sort_order' => 8])
    ->row(['name' => 'C#', 'sort_order' => 9])
    ->row(['name' => '.NET', 'sort_order' => 10])
    ->row(['name' => 'Python', 'sort_order' => 11])
    ->row(['name' => 'PostgreSQL', 'sort_order' => 12])
    ->row(['name' => 'MySQL', 'sort_order' => 13])
    ->row(['name' => 'Docker', 'sort_order' => 14])
    ->row(['name' => 'REST API', 'sort_order' => 15])
    ->row(['name' => 'HTML', 'sort_order' => 16])
    ->row(['name' => 'CSS', 'sort_order' => 17]);
