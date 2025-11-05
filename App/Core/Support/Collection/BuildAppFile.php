<?php 
namespace App\Core\Support\Collection;
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

class BuildAppFile {

    public function __construct(public array $files)
    {
        
    }

    /**
     * Summary of __get
     * @property string $folder
     * @property string $menu
     * @property string $middleware
     * @property string $routes
     */
    public function __get($name){
        return $this->loadConfig($name);
    }

      private function loadConfig(string $name): mixed
    {
        // Se già caricato, restituiscilo
        if (array_key_exists($name, $this->files)) {
            return $this->files[$name];
        }

        // Percorso file config
        $file = $this->basePath . DIRECTORY_SEPARATOR . $name . '.php';

        if (is_file($file)) {
            $data = include $file;
            $this->files[$name] = $data;
            return $data;
        }

        throw new \InvalidArgumentException("Config file '{$name}.php' non trovato in {$this->basePath}");
    }
    public function all(){
        return $this->files;
    }

}