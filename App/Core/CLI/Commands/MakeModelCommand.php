<?php
namespace App\Core\CLI\Commands;

use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;

class MakeModelCommand implements CommandInterface
{
    public function exe(array $args = []): void
    {
        $modelName = trim($args[2]) ?? null;
        if (!$modelName || $modelName = "") 
           Out::error("You must specify a name for the template");
       
        $path = __DIR__ . '/../../../App/Models/' . $modelName . '.php';
        if (file_exists($path)) 
            Out::warn("The Model already exists.");
        
        $template = "<?php\n\nnamespace App\Models;\n\nclass $modelName\n{\n    // Model $modelName\n}\n";
        file_put_contents($path, $template);

        Out::success( "✅ Modello '$modelName' creato con successo in App/Models/$modelName.php\n");

    }
}