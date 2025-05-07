<?php 
namespace App\Core\CLI\Commands;

use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;

class MakeTableCommand implements CommandInterface {
    public function exe(array $command) {
        
        Out::success('ok');
    }

    
}