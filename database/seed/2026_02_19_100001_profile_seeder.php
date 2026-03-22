<?php

use App\Core\DataLayer\Seeder\Seeder;

return Seeder::table('profile')
    ->row([
        'name' => 'Iadicola',
        'tagline' => 'Programmatore - Napoli',
        'welcome_message' => 'Web Application Engineer PHP, Java, C#, React',
        'selected' => 1,
    ]);
