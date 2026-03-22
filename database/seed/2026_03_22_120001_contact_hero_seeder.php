<?php

use App\Core\DataLayer\Seeder\Seeder;

return Seeder::table('contact_hero')
    ->row([
        'badge' => '// open to work',
        'title_html' => 'Costruiamo qualcosa<br><em>insieme</em>',
        'description_html' => 'Software Engineer con esperienza in <strong>PHP</strong>, <strong>Java</strong>, <strong>React</strong> e <strong>C#</strong>.<br>Ho progettato sistemi ERP, integrazioni marketplace, gestionali sanitari e piattaforme enterprise.<br>Contattami per dare vita alla tua idea.',
        'primary_stat_value' => 'Dal 2020',
        'primary_stat_label' => 'Nel campo',
        'secondary_stat_value' => '10+',
        'secondary_stat_label' => 'Progetti realizzati',
        'technology_stat_label' => 'Tecnologie',
    ]);
