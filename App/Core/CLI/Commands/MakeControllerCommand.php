<?php

declare(strict_types=1);

namespace App\Core\CLI\Commands;

use App\Core\CLI\Stubs\StubGenerator;
use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;
use App\Core\Helpers\Str;

class MakeControllerCommand implements CommandInterface
{
    public function exe(array $command): void
    {
        $name = $command[2] ?? null;

        if (!$name) {
            Out::error('You must specify a controller name.');
            return;
        }

        $className = Str::studly($name);
        if (!str_ends_with($className, 'Controller')) {
            $className .= 'Controller';
        }

        $filePath = getcwd() . '/App/Controllers/' . $className . '.php';

        $saved = StubGenerator::make('controller')
            ->replace(['{{CLASS}}' => $className])
            ->saveTo($filePath);

        if (!$saved) {
            Out::warn("Controller already exists: App/Controllers/{$className}.php");
            return;
        }

        Out::success("Controller created: App/Controllers/{$className}.php");
    }
}
