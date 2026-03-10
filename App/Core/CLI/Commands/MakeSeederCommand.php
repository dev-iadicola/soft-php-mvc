<?php

declare(strict_types=1);

namespace App\Core\CLI\Commands;

use App\Core\CLI\Stubs\StubGenerator;
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

        $filePath = getcwd() . '/Database/seed/' . $fileName;

        StubGenerator::make('seeder')
            ->replace(['{{TABLE}}' => $table])
            ->saveTo($filePath);

        Out::ln("Created seeder: $fileName");
    }

    private function guessTableName(string $name): string
    {
        $name = strtolower($name);
        $name = preg_replace('/_seeder$/', '', $name);
        return $name;
    }
}
