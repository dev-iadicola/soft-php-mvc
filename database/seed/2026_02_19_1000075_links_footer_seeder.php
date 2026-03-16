<?php

use App\Core\DataLayer\Seeder\Seeder;

return Seeder::table('links_footer')
    ->row(['title' => 'Home', 'link' => '/'])
    ->row(['title' => 'Contatti', 'link' => '/contatti'])
    ->row(['title' => 'Portfolio', 'link' => '/portfolio'])
    ->row(['title' => 'Progetti', 'link' => '/progetti'])
    ->row(['title' => 'Tech Stack', 'link' => '/tech-stack'])
    ->row(['title' => 'Partners', 'link' => '/partners']);
