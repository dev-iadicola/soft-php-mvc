<?php
namespace App\Core\Support\Tree;

use App\Core\Mvc;

class TreeProject {
    public function __construct(public Mvc $mvc) {
        $this->generateTree();
    }


    private function generateTree(string $direcotry = realpath('/'), string $prefix = ""): string{
        $items = scandir($direcotry);
        $items = array_diff($items,['.', '..', 'vendor', 'node_modules', '.git']);

        $result = '';

        foreach ($items as $item) {
            $path = "$direcotry/$item";

            $result .= $prefix.'|-- '.$item . "\n";

            if(is_dir($path)){
                $result .= $this->generateTree($path, $prefix . '|   ');
            }
        }
        return $result;
    }

    private function putIntoFile(){

        
    }


}

