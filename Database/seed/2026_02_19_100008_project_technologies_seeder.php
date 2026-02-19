<?php

use App\Core\DataLayer\Seeder\Seeder;

// Mapping basato sull'ordine di inserimento:
// Projects: 1=Personal MVC, 2=Ecommerce, 3=Shop Online, 4=BISTROT,
//           5=Libreria C#, 6=Introduzione LM, 7=Ice Cream, 8=Color Grading, 9=Appointment Scheduling
// Technology: 1=PHP, 2=Laravel, 3=React, 4=JavaScript, 5=C#, 6=.NET, 7=Python, 8=MySQL, 9=HTML, 10=CSS

return Seeder::table('project_technologies')
    // Personal MVC -> PHP, MySQL
    ->row(['project_id' => 1, 'technology_id' => 1])
    ->row(['project_id' => 1, 'technology_id' => 8])
    // Ecommerce -> PHP, Laravel, MySQL
    ->row(['project_id' => 2, 'technology_id' => 1])
    ->row(['project_id' => 2, 'technology_id' => 2])
    ->row(['project_id' => 2, 'technology_id' => 8])
    // Shop Online -> React, JavaScript
    ->row(['project_id' => 3, 'technology_id' => 3])
    ->row(['project_id' => 3, 'technology_id' => 4])
    // BISTROT -> React, JavaScript
    ->row(['project_id' => 4, 'technology_id' => 3])
    ->row(['project_id' => 4, 'technology_id' => 4])
    // Libreria C# -> C#, .NET
    ->row(['project_id' => 5, 'technology_id' => 5])
    ->row(['project_id' => 5, 'technology_id' => 6])
    // Introduzione LM -> Python
    ->row(['project_id' => 6, 'technology_id' => 7])
    // Ice Cream -> HTML, CSS, JavaScript
    ->row(['project_id' => 7, 'technology_id' => 9])
    ->row(['project_id' => 7, 'technology_id' => 10])
    ->row(['project_id' => 7, 'technology_id' => 4])
    // Color Grading -> JavaScript, HTML
    ->row(['project_id' => 8, 'technology_id' => 4])
    ->row(['project_id' => 8, 'technology_id' => 9])
    // Appointment Scheduling -> React, JavaScript
    ->row(['project_id' => 9, 'technology_id' => 3])
    ->row(['project_id' => 9, 'technology_id' => 4]);
