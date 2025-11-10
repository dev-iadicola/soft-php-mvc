<?php

namespace App\Core\CLI\Commands;

use App\Core\CLI\Commands\Validation\ValidateClassName;
use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;

class MakeMiddlewareCommand implements CommandInterface
{

    public function exe(array $params)
    {
        // helper: ricorda, l'array $params[2] contiene la stringa per creare il middleware. 
        // Altrimenti la crea tramite readline
        while (!isset($params[2])) {
            Out::info("Welcome to terminale, you choose to create a middleware!");
            Out::info('Enter the name of the middleware class. Or 0 to exit.');
            $name = readline("> ");
            switch ($name) {
                case '0':
                    Out::ln("Exit, thanks you for use Soft CLI");
                    return;

                case '':
                    Out::warn("The name is empty");
                    continue 2;
                default:
                    try {
                        ValidateClassName::Validate($name, 'Middleware');
                        $params[2] = $name;
                        Out::ok("✅ Class name '$name' accepted!");
                        break 2;
                        // esce sia dallo switch che dal while
                    } catch (\InvalidArgumentException $e) {
                        Out::error($e->getMessage());
                        continue 2;
                    }
            }
        }
        // end while 

        $this->createMiddleware($params[2]);
    }

    private function createMiddleware(string $name): void
    {   // First letter uppercase
        $name = ucfirst($name);
        // if the command has middleware class i needed validate name
         ValidateClassName::Validate($name, 'Middleware');
        $path = "App/Middleware/{$name}.php";

        // Se già esiste, avvisa ma non interrompere
        if (file_exists($path)) {
            Out::warn("⚠️  Middleware '{$name}' already exists at {$path}");
            return;
        }

        $template = <<<PHP
<?php

namespace App\\Middleware;

use App\\Core\\Http\\Request;
use App\\Core\\Contract\\MiddlewareInterface;

class {$name} implements MiddlewareInterface
{
    public function exec(Request \$request)
    {
        // TODO: implement your middleware logic
    }
}
PHP;

        // Withe file in App
        file_put_contents($path, $template);

        

        Out::ln("──────────────────────────────────────────────");
        Out::info("Generated class:\n");

        // Legge e mostra il contenuto del file appena creato
        $content = file_get_contents($path);

        // Evidenzia codice con colori base ANSI
        $lines = explode("\n", $content);
        foreach ($lines as $i => $line) {
            $lineNumber = str_pad((string)($i + 1), 3, ' ', STR_PAD_LEFT);
            // stampa numero linea in grigio e codice in verde chiaro
            echo "\033[90m{$lineNumber} | \033[0;32m{$line}\033[0m\n";
        }

        Out::ln("──────────────────────────────────────────────");
        // ✅ Mostra risultato formattato nel terminale
        Out::ok("Middleware '{$name}' created successfully!");
        Out::ln("Path: {$path}");
        Out::ln("Config your Middleware in config/middleware.php");
    }
}
