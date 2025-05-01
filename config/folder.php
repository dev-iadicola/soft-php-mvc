<?php 
/**
 * semplicità, migliorando il codice, senza dover scrivere molto codice
 * 
 * questo file permette di ridurre il codice per trovare le cartelle
 */
$root = $_SERVER['DOCUMENT_ROOT'];

return [
   
    'root' => $root,
    'views' => $root.'/views',
    'mails'=> $root.'/mails',
    'storage' =>[
        'base'=> $root.'/storage/',
        'images' => $root.'/storage/images/',
    ],
];
