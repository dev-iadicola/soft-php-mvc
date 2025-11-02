<?php

namespace App\Core;

use App\Core\Mvc;


class Middleware{
    public function __construct(
        public Mvc $mvc, 
        // Array config/middleware.php definito 
        public array|object $queueForBaseRoute = [],
        // Gestione richiesta controller
        public array $queueRoute = []
    ){
       
    }

    public function register($middleware){
        array_push($this->queueRoute, $middleware);
    }

    public function execute(){
        // Prendiamo la request URI
        $requestPath = $this->mvc->request->getRequestPath();
       
        // Prendiamo l'array presente nel file /congif/middleware.php
       $middlewareFileArray = $this->queueForBaseRoute;


        foreach($middlewareFileArray as $basePath => $middelwareGroup ){
            
            if(str_starts_with($requestPath, $basePath)){
                $this->executeMiddleware($middelwareGroup); // Middleware da utilizzare per la richiesta effettuata
            }
            
        }
     
        //per middleware specifiche
        $this->executeMiddleware($this->queueRoute);
    }

    private function executeMiddleware($middelwareGroup){
        foreach($middelwareGroup as $middleware){
            (new $middleware)->exec($this->mvc); // Eseguiamo il middleware secondo la richiesta svolta
        }
    }
}