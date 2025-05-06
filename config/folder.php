<?php

/**
 * semplicità, migliorando il codice, senza dover scrivere molto codice
 * 
 * questo file permette di ridurre il codice per trovare le cartelle
 */

use App\Core\Services\FolderApp;

$folders = new FolderApp();
$folders::set('root');
$folders::set('views','views');
$folders::set('mails','mails');
$folders::set('storage', 'storage');
$folders::set('images', 'storage.images');
$folders::set('css','assets.css');

return $folders;

