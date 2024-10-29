<?php 
/**
 * semplicità, migliorando il codice, senza dover scrivere molto codice
 * 
 * questo file perette di ridurre il codice per trovare le cartelle
 */
$root = $_SERVER['DOCUMENT_ROOT'];

return [
   
    'root' => $root,
    'views' => $root.'/views',
    'mails'=> $root.'/mails',
    'uploads' =>[
        'base'=> $root.'/uploads/',
        'image' => $root.'/uploads/images/',
    ] ,
    
    
];
