<?php
namespace App\Core\CLI\Commands;

use App\Core\Mvc;
use App\Core\Config;
use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;

class MakeModelCommand implements CommandInterface
{
    public function exe(array $command): void
    {
        $modelName = $command[2] ?? null;
        if (!$modelName || $modelName = "")
            Out::error("You must specify a name for the template");
        $mvc = $this->mvc();
        // $path = __DIR__ . '/../../../App/Models/' . $modelName . '.php';
        // if (file_exists($path)) 
        //     Out::warn("The Model already exists.");

        // $template = "<?php\n\nnamespace App\Models;\n\nclass $modelName\n{\n    // Model $modelName\n}\n";
        // file_put_contents($path, $template);

        Out::success("Modello " . $modelName . " creato con successo in App/Models/$modelName.php\n");

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