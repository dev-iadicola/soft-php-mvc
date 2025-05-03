<?php

/**
 * semplicità, migliorando il codice, senza dover scrivere molto codice
 * 
 * questo file permette di ridurre il codice per trovare le cartelle
 */

use App\Core\Services\FolderApp;

FolderApp::set('root');
FolderApp::set('views','views');
FolderApp::set('mails','mails');
FolderApp::set('storage', 'storage');
FolderApp::set('images', 'storage.images');

return new FolderApp();

