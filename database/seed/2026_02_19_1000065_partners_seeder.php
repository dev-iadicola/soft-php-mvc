<?php

use App\Core\DataLayer\Seeder\Seeder;

return Seeder::table('partners')
    ->row([
        'name' => 'Green Tech Solution',
        'website' => 'https://greentechsolution.it',
    ])
    ->row([
        'name' => 'Schindler',
        'website' => 'https://www.schindler.com',
    ])
    ->row([
        'name' => 'Sagres',
        'website' => 'https://www.sagres.it',
    ])
    ->row([
        'name' => 'Emodial',
        'website' => 'https://emodai.emodial.it/',
    ]);
