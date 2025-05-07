<?php 
namespace App\Core\CLI\Commands;

use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;
class MakeMigrationCommand implements CommandInterface {

    public function exe(array $command) {

        Out::success("We are in ".__CLASS__);
    }
}