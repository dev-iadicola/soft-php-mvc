<?php

use App\Core\DataLayer\Seeder\Seeder;

$termsPath = __DIR__ . '/../../views/pages/laws/policy.php';
$cookiePath = __DIR__ . '/../../views/pages/laws/cookie-law.php';

$termsText = file_get_contents($termsPath);
$cookieText = file_get_contents($cookiePath);

if ($termsText === false) {
    $termsText = '';
}

if ($cookieText === false) {
    $cookieText = '';
}

return Seeder::table('laws')
    ->row([
        'id' => 2,
        'title' => 'Termini e Condizioni',
        'testo' => $termsText,
    ])
    ->row([
        'id' => 3,
        'title' => 'Cookie Policy',
        'testo' => $cookieText,
    ]);
