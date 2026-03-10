<?php

declare(strict_types=1);

namespace App\Core\CLI\Commands;

use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;

class MakeControllerCommand implements CommandInterface
{
    public function exe(array $params): void
    {
        $name = $params[2] ?? null;

        if (!$name) {
            Out::error('You must specify a controller name.');
            return;
        }

        $className = str_ends_with($name, 'Controller') ? $name : $name . 'Controller';
        $controllerDir = getcwd() . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Controllers';
        $filePath = $controllerDir . DIRECTORY_SEPARATOR . $className . '.php';

        if (!is_dir($controllerDir)) {
            mkdir($controllerDir, 0755, true);
        }

        if (file_exists($filePath)) {
            Out::warn("Controller already exists: App/Controllers/{$className}.php");
            return;
        }

        $content = <<<PHP
<?php

namespace App\Controllers;

use App\Core\Controllers\Controller;

class {$className} extends Controller
{
    public function index(): void
    {
    }
}
PHP;

        file_put_contents($filePath, $content . PHP_EOL);

        Out::success("Controller created: App/Controllers/{$className}.php");
    }
}
