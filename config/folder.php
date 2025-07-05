<?php

/**
    * Configuration file for folder paths
 */

use App\Core\Services\FolderApp;

$folders = new FolderApp();
$folders::set('root');
$folders::set('views','views');
$folders::set('mails','mails');
$folders::set('storage', 'storage');
$folders::set('images', 'storage.images');
$folders::set('migration','database.migration');
$folders::set('css','assets.css');

return $folders;

