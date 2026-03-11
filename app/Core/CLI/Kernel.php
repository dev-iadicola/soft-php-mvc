<?php

declare(strict_types=1);

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
use App\Core\CLI\Commands\RectorCommand;
use App\Core\CLI\Commands\MakeRepositoryCommand;
use App\Core\CLI\Commands\MakeServiceCommand;
use App\Core\CLI\Commands\ModelInspectCommand;
use App\Core\CLI\Commands\AnalyseCommand;
use App\Core\CLI\Commands\Route\RouteListCommand;
use App\Core\CLI\Commands\Route\RouteCacheCommand;
use App\Core\CLI\Commands\Route\RouteClearCommand;

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
    protected function registerCommands(): void
    {
        $this->commands = [
            'make:model' => MakeModelCommand::class,
            'make:controller' => MakeControllerCommand::class,
            'make:mw' => MakeMiddlewareCommand::class,
            'make:repository' => MakeRepositoryCommand::class,
            'make:service' => MakeServiceCommand::class,
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

            // Inspect
            'model:inspect' => ModelInspectCommand::class,

            // Testing & Quality
            'test' => TestCommand::class,
            'rector' => RectorCommand::class,
            'analyse' => AnalyseCommand::class,

            // Route commands
            'route:list' => RouteListCommand::class,
            'route:cache' => RouteCacheCommand::class,
            'route:clear' => RouteClearCommand::class,

            // Clear commands
            'clear:cache' => ClearCacheCommand::class,
        ];
    }

    public function handle(array $argv): void
    {
        $commandClass = $this->validateCommand($argv);

        $instance = new $commandClass();

        $instance->exe($argv);
    }


    private function validateCommand(array $argv): string
    {
        $command = $argv[1] ?? null;
        if (!$command) {
            Out::info("Welcome to SoftCLI v1.0\nA lightweight PHP CLI tool for your project (in development).\n");
            Out::ln("Available commands:");
            Out::ln("  make:controller   Create a new controller");
            Out::ln("  make:model        Create a new model (-m migration, -c controller, -r repository, -s service)");
            Out::ln("  make:mw           Create a new middleware");
            Out::ln("  make:repository   Create a new repository");
            Out::ln("  make:service      Create a new service");
            Out::ln("  make:migration    Create a new migration");
            Out::ln("  make:seeder       Create a new seeder");
            Out::ln("  migrate           Run DB migrations");
            Out::ln("  migrate:rollback  Rollback last migration");
            Out::ln("  migrate:status    Show migration status");
            Out::ln("  seed              Run seeders");
            Out::ln("  model:inspect     Inspect a model's properties");
            Out::ln("  serve             Start dev server");
            Out::ln("  test              Run PHPUnit tests");
            Out::ln("  analyse           Run static analysis");
            Out::ln("  route:list        List all registered routes");
            Out::ln("  route:cache       Cache routes for production");
            Out::ln("  route:clear       Clear the route cache");
            Out::ln("  clear:cache       Clear application cache");
            Out::ln("\nUsage: php soft <command> [options]");
            Out::ln("Example: php soft make:model Post -mcrs");
            exit();
        }        
       
        // NOTE: qui avviene la verifica se il comando esiste nella lista dei comandi registrati
        if (!isset($this->commands[$command])) {
            Out::error("The command '{$command}' does not exist.");
            exit(1);
        }
        $commandClass = $this->commands[$command];

        if (!class_exists($commandClass)) {
            Out::error("Command class {$commandClass} does not exist.");
            exit(1);
        }

        return $commandClass;
    }
}
