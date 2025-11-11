<?php

namespace App\Core\Support\Collection;

use App\Traits\Attributes;


/**
 * @property-read array controllers
 * @property-read array filesystem
 * @property-read array menu
 * @property-read array middleware
 * @property-read array routes
 * @property-read array storage use filesystem
 * @property-read array settings
 *
 * @method array controllers
 * @method array filesystem
 * @method array resources
 * @method array menu
 * @method array middleware
 * @method array settings
 * @method array storage use filesystem
 */
class ConfigCollection
{
    use Attributes;

    protected string $basePath;

    public function __construct(array $files)
    {
        // Populate array attributes with the all folders in dir 'config', 
        //  It's be useful for the magic getter and setter in the trait Attributes
        $this->attributes = $files;
    }

    public function all()
    {
        return $this->attributes;
    }
}
