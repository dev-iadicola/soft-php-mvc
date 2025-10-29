<?php
namespace App\Core\Support\Tree;

use App\Core\Mvc;

class TreeProject
{
    private $arrayResult = [];
    public function __construct(public Mvc $mvc)
    {
        $baseroot = $this->mvc->config->folder->root;
        $this->generateTree($baseroot);
    }


    public function generateTree(?string $directory, string $prefix = ""): void
    {
        if (is_null($directory) || empty($directory)) {
            $directory = baseRoot();
        }
  
        $items = scandir($directory);

        $items = array_diff($items, ['.', '..', 'vendor', 'node_modules', '.git']);

        foreach ($items as $item) {
            $path = "$directory". DIRECTORY_SEPARATOR."$item";

            if (is_dir($path)) {
                $this->arrayResult[$item] = $path ;
                $this->generateTree($path, $prefix . '|   ');
            
            }
        }
      
    }



    private function putIntoFile()
    {
        Storage::make($this->generateTree());

    }


}

