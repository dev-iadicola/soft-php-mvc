<?php
namespace App\Core\CLI\Commands;

use App\Core\Mvc;
use App\Core\Config;
use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;

class StorageCommand implements CommandInterface
{
    public function exe(array $command): void
    {
        if(isset($command[3])){
            $this->registerCommands($command);
        }
        OUt::info($command[0] . ' ' . $command[1] . ' ' . $command[2] ?? ' '. $command[3]??' ' .$command[4]??'');
        Out::success("Command of storage executed successfully.");
        exit();

    }

    private function registerCommands($command)
    {
        $listOfCommands = [
            '--link' => 'Link the storage directory to the public directory',
            '--clear' => 'Clear the storage directory',
            '--backup' => 'Backup the storage directory',
            '--create' => $this->create($command[4]),
        ];
        return $listOfCommands[$command[3]];
    }

    private function create($disk){
        mkdir(getcwd() . "/storage/$disk", 0775, true);
    }

    
    private function mvc(): Mvc
    {
        echo getcwd() . '/.env';
        Config::env(getcwd() . '/.env');
        $config = Config::dir(getcwd() . '/config');
        setMvc($config);
        return mvc();
    }
}