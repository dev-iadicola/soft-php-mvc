<?php

/**
    * Configuration file for folder paths
 */

use App\Core\Services\FolderApp;

/**
 * @var mixed
 * Ogni folder::set ritorna baseroot() ossia  $_SERVER['DOCUMENT_ROOT'], di seguito basta partire dalla base
 */
$folders = new FolderApp();
$folders::set('root');
$folders::set('views','views');
$folders::set('mails','mails');
$folders::set('storage', 'storage');
$folders::set('images', 'storage.images');
$folders::set('migration','database.migration');
$folders::set('css','assets.css');
$folders::set('controllers','App.Controllers');

return $folders;


