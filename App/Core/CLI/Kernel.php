<?php 
namespace App\Core\CLI;

use App\Core\CLI\Commands\MakeControllerCommand;
use App\Core\CLI\Commands\MakeModelCommand;
use App\Core\CLI\System\Out;

class Kernel {
    protected array $commands = [];

    public function __construct(){
        $this->registerCommands();
    }

    protected function registerCommands(){
        $this->commands = [
            'make:model' => MakeModelCommand::class,
            'make:controller'=> MakeControllerCommand::class,
        ];
    }

    public function handler($argv){

        $commandClass = $this->validateCommand($argv);

       $istance = new $commandClass();

       $istance->exe($argv);

    
    }
    
    private function validateCommand($argv){

        $command = $argv[1];
        if(!$command){
            Out::error("Please provide a comman");
        }


        if(!isset($this->commands[$command])){
            Out::error(" the command '$command' not exist.");
        }

        $commandClass = $this->commands[$command];

        if(!class_exists($commandClass)){
            Out::error("Command class $commandClass don't exist.");
        }

        return $commandClass;
    
    }
    
    
   
}