<?php

namespace App\Model;

use PDO;
use App\Core\ORM;
 class Portfolio extends ORM{

    /**
     * Summary of table
     * @var string $table 
     * Questa variabile è importante per poter inserire staticamente il nome della colonna 
     * permettendoci di rispamiare tempo
     * 
     */
     static string $table = 'portfolio'; 
     static array $fillable = [
        'title','overview','link'
     ];
    
 }