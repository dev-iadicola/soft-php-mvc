<?php

namespace App\Core\CLI;

use App\Core\CLI\System\Out;
use App\Core\CLI\Commands\ServeCommand;
use App\Core\CLI\Commands\MakeModelCommand;
use App\Core\CLI\Commands\MakeMigrationCommand;
use App\Core\CLI\Commands\MakeControllerCommand;
use App\Core\CLI\Commands\MigrateCommand;
use App\Core\CLI\Commands\MigrateRollbackCommand;
use App\Core\CLI\Commands\MigrateStatusCommand;
use App\Core\CLI\Commands\Clear\ClearCacheCommand;
use App\Core\CLI\Commands\MakeMiddlewareCommand;
use App\Core\CLI\Commands\MakeSeederCommand;
use App\Core\CLI\Commands\SeedCommand;
use App\Core\CLI\Commands\SeedRollbackCommand;
use App\Core\CLI\Commands\SeedStatusCommand;
use App\Core\CLI\Commands\StorageCommand;
use App\Core\CLI\Commands\TestCommand;

class Kernel
{
    protected array $commands = [];

    public function __construct()
    {
        $this->registerCommands();
    }

    /**
     * Summary of registerCommands
     * @return void
     * Qui registri i comandi disponibili per il CLI.
     * Ogni comando è associato a una classe che implementa l'interfaccia CommandInterface.
     */
    protected function registerCommands()
    {
        $this->commands = [
            'make:model' => MakeModelCommand::class,
            'make:controller' => MakeControllerCommand::class,
            'make:mw' => MakeMiddlewareCommand::class,
            'make:migration' => MakeMigrationCommand::class,
            'migrate' => MigrateCommand::class,
            'migrate:rollback' => MigrateRollbackCommand::class,
            'migrate:status' => MigrateStatusCommand::class,
            'make:seeder' => MakeSeederCommand::class,
            'seed' => SeedCommand::class,
            'seed:rollback' => SeedRollbackCommand::class,
            'seed:status' => SeedStatusCommand::class,
            'serve' => ServeCommand::class,
            'print' => Out::class,
            'storage' => StorageCommand::class,

            // Testing
            'test' => TestCommand::class,

            // Clear commands
            'clear:cache' => ClearCacheCommand::class,
        ];
    }

    public function handler($argv)
    {

        $commandClass = $this->validateCommand($argv);

        $istance = new $commandClass();

        $istance->exe($argv);
    }


    private function validateCommand($argv)
    {
        $command = $argv[1] ?? null;
        if (!$command) {
            Out::info("Welcome to SoftCLI v1.0\nA lightweight PHP CLI tool for your project (in development).\n");
            Out::ln("Future available commands:");
            Out::ln("  make:controller   Create a new controller");
            Out::ln("  make:model        Create a new model");
            Out::ln("  make:table        Create a new table");
            Out::ln("  migrate           Run DB migrations");
            Out::ln("  serve             Start dev server");
            Out::ln("  test              Run PHPUnit tests");
            Out::ln("\nUsage: php soft <command> [options]");
            Out::ln("Example: php soft make:controller UserController");
            exit();
        }        
       
        // NOTE: qui avviene la verifica se il comando esiste nella lista dei comandi registrati
        if (!isset($this->commands[$command])) {
            Out::error(" the command '$command' not exist.");
        }
        $commandClass = $this->commands[$command];

        if (!class_exists($commandClass)) {
            Out::error("Command class $commandClass don't exist.");
        }

        return $commandClass;
    }
}
