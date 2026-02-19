<?php

namespace App\Core\CLI\Commands;

use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;

class MakeSeederCommand implements CommandInterface
{
    public function exe(array $command): void
    {
        $name = $command[2] ?? null;
        if (!$name) {
            $name = trim(readline('Seeder name (e.g. users_seeder): '));
        }
        if (!$name) {
            Out::error('You must specify a seeder name.');
            return;
        }

        $table     = $this->guessTableName($name);
        $timestamp = date('Y_m_d_His');
        $fileName  = "{$timestamp}_{$name}.php";

        $seederDir = getcwd() . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'seed';
        if (!is_dir($seederDir)) {
            mkdir($seederDir, 0755, true);
        }

        $stub    = file_get_contents(__DIR__ . '/../Stubs/seeder.stub');
        $content = str_replace('{{TABLE}}', $table, $stub);
        file_put_contents($seederDir . DIRECTORY_SEPARATOR . $fileName, $content);

        Out::ln("Created seeder: $fileName");
    }

    private function guessTableName(string $name): string
    {
        $name = strtolower($name);
        $name = preg_replace('/_seeder$/', '', $name);
        return $name;
    }
}
