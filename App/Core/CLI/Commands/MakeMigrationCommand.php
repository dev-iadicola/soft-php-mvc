<?php
namespace App\Core\CLI\Commands;


use App\Core\Config;
use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;
use App\Core\Exception\FileNotFoundException;
use App\Core\Support\Collection\BuildAppFile;

class MakeMigrationCommand implements CommandInterface
{



    public function exe(array $command)
    {

        Out::info("We are in " . __CLASS__);

        $this->config(); // config to get propriety of file env

        $pathMigration = getcwd() . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migration' . DIRECTORY_SEPARATOR . 'schema-config.php';
        if (!file_exists($pathMigration)) {
            throw new FileNotFoundException($pathMigration);
        }

        $schemaFns = include $pathMigration;

        foreach ($schemaFns as $fn) {
            if (is_callable($fn)) {
                $fn(); // esegui ciascuna migrazione
            }
        }

       



    }

    private function config(): BuildAppFile
    {
        Config::env(getcwd() . '/.env');

        $config = Config::dir(getcwd() . '/config');

        return $config;

    }


}