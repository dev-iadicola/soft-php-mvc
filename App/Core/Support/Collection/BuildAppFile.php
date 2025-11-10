<?php

namespace App\Core\Support\Collection;

use App\Traits\Attributes;


/**
 * @property-read array $controllers
 * @property-read array|object $folder
 * @property-read array $menu
 * @property-read array $middleware
 * @property-read array $routes
 * @property-read array $storage
 * @property-read array $settings
 *
 * @method array controllers()
 * @method array folder()
 * @method array menu()
 * @method array middleware()
 * @method array routes()
 * @method array storage()
 */
class BuildAppFile
{
    use Attributes;

    protected string $basePath;

    public function __construct(array $files) {
        // Popoliamo l'array attributes che viene inizializzato nel trati Attributes, esso servira per il getter e setter magico.
        $this->attributes = $files;
    }

    /**
     * Summary of __get
     * @property string $folder
     * @property string $menu
     * @property string $middleware
     * @property string $routes
     * @property string $settings
     * @property string $storage
     */
    // private function loadConfig(string $name): mixed
    // {
    //     // Se già caricato, restituiscilo
    //     if (array_key_exists($name, $this->files)) {
    //         return $this->attributes[$name];
    //     }

    //     // Percorso file config
    //     $file = $this->basePath . DIRECTORY_SEPARATOR . $name . '.php';

    //     if (is_file($file)) {
    //         $data = include $file;
    //         $this->attributes[$name] = $data;
    //         return $data;
    //     }

    //     throw new \InvalidArgumentException("Config file '{$name}.php' non trovato in {$this->basePath}");
    // }
    public function all()
    {
        return $this->attributes;
    }
}
