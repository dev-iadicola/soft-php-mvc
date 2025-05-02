<?php 
namespace App\Core\Support\Collection;

class BuildAppFile {

    public function __construct(public array $files)
    {
        
    }

    public function __get($name){
        return $this->files[$name];
    }

    public function all(){
        return $this->files;
    }

}