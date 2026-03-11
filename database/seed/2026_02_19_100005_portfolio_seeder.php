<?php

use App\Core\DataLayer\Seeder\Seeder;

return Seeder::table('portfolio')
    ->row([
        'title' => 'React Hook',
        'overview' => '<p>Una Guida su React Hook per chi vuole introdursi nel mondo front-end</p>',
        'link' => 'https://androluix.github.io/React-Hook/',
        'deploy' => 1,
    ]);
