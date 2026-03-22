<?php

use App\Core\DataLayer\Seeder\Seeder;

return Seeder::table('links_footer')
    ->row(['title' => 'Home', 'link' => '/', 'sort_order' => 1])
    ->row(['title' => 'Contatti', 'link' => '/contatti', 'sort_order' => 2])
    ->row(['title' => 'Portfolio', 'link' => '/portfolio', 'sort_order' => 3])
    ->row(['title' => 'Progetti', 'link' => '/progetti', 'sort_order' => 4])
    ->row(['title' => 'Tech Stack', 'link' => '/tech-stack', 'sort_order' => 5])
    ->row(['title' => 'Partners', 'link' => '/partners', 'sort_order' => 6]);
