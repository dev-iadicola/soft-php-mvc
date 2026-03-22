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
        if (!$name || str_starts_with($name, '-')) {
            Out::error('You must specify a seeder name. Example: php soft make:seeder users_seeder');
            return;
        }

        try {
            $table     = $this->guessTableName($name);
            $timestamp = date('Y_m_d_His');
            $fileName  = "{$timestamp}_{$name}.php";

            $filePath = getcwd() . '/database/seed/' . $fileName;

            $saved = StubGenerator::make('seeder')
                ->replace(['{{TABLE}}' => $table])
                ->saveTo($filePath);

            if (!$saved) {
                Out::warn("Seeder already exists: database/seed/{$fileName}");
                return;
            }

            Out::success("Seeder created: database/seed/{$fileName}");
        } catch (\Throwable $e) {
            Out::error("Failed to create seeder: {$e->getMessage()}");
        }
    }

    private function guessTableName(string $name): string
    {
        $name = strtolower($name);
        $name = preg_replace('/_seeder$/', '', $name);
        return $name;
    }
}
