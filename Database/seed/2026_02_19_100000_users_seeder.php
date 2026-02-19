<?php

use App\Core\DataLayer\Seeder\Seeder;

return Seeder::table('users')
    ->row([
        'email' => 'admin@example.com',
        'password' => password_hash('password', PASSWORD_BCRYPT),
    ]);
