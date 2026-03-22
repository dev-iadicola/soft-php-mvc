<?php

use App\Core\DataLayer\Seeder\Seeder;

return Seeder::table('contact_cards')
    ->row([
        'icon' => 'fa-globe',
        'color' => 'green',
        'title' => 'Applicazioni Web & ERP',
        'description' => 'Piattaforme gestionali, e-commerce, dashboard analitiche e sistemi multi-canale.',
        'tags' => 'Laravel, React, Filament',
        'sort_order' => 1,
    ])
    ->row([
        'icon' => 'fa-cogs',
        'color' => 'blue',
        'title' => 'API & Integrazioni',
        'description' => 'API REST, integrazioni marketplace (eBay, Amazon SP-API), sincronizzazione dati.',
        'tags' => 'REST, Spring Boot, OAuth',
        'sort_order' => 2,
    ])
    ->row([
        'icon' => 'fa-sitemap',
        'color' => 'purple',
        'title' => 'Architettura Software',
        'description' => 'Design patterns, architetture MVC, Layered, Hexagonal e SOA. Code review.',
        'tags' => 'SOLID, Clean Code, DDD',
        'sort_order' => 3,
    ])
    ->row([
        'icon' => 'fa-wrench',
        'color' => 'orange',
        'title' => 'Refactoring & DevOps',
        'description' => 'Ottimizzazione codice, containerizzazione, CI/CD e gestione ambienti di deploy.',
        'tags' => 'Docker, CI/CD, Git',
        'sort_order' => 4,
    ]);
