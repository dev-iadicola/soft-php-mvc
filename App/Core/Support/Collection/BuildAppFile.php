<?php 
namespace App\Core\Support\Collection;

class BuildAppFile {

    public function __construct(public array $files)
    {
        
    }

    /**
     * Summary of __get
     * @property string $folder
     * @property string $menu
     * @property string $middleware
     * @property string $routes
     */
    public function __get($name){
        return $this->files[$name];
    }

    public function all(){
        return $this->files;
    }

}