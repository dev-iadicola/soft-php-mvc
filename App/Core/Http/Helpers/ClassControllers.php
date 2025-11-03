<?php 
namespace App\Core\Http\Helpers;

use IteratorAggregate;
use Traversable;
use ArrayIterator;
use ReflectionClass;

class ClassControllers implements IteratorAggregate {
    /** @var array<string, Stack> */
    private array $stacks;

    public function reflectionController($className){
        $controllerName = $this->stacks[$className];
        return new ReflectionClass($controllerName);
    }

    public function reflectionAllControllers(){
        $controllerRelfected = [];
        foreach($this->stacks as $className => $stack){
            $controllerRelfected[] = $this->reflectionController($className);
        }
        return $controllerRelfected;
    }

  
    public function addController(string $className){
        $this->stacks[$className] = new Stack();
    }
    
    /**
     * Summary of find
     * @param string $className
     * @return Stack
     */
    public function find(string $className):Stack{
     return   $this->stacks[$className];
    }
    /**
     * Summary of setStack
     * @param string $className
     * @param \App\Core\Http\Collection\Stack $stack
     * @return void
     */
    public function setStack(string $className, Stack $stack){
        $this->stacks[$className] = $stack;
    }

    /**
     * Summary of all
     * @return Stack[]
     */
    public function all(): array{
        return $this->stacks;
    }

    /**
     * Summary of getIterator
     *  Allow to iterate in a loop.
     * @return ArrayIterator
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->stacks);
    }







    
   

}