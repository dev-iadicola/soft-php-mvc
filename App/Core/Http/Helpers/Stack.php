<?php

namespace App\Core\Http\Helpers;

use App\Core\Support\Collection;
use ArrayAccess;

class Stack
{

    private Collection $middleware;
    private Collection $path;

    

    public function __construct()
    {
        $this->middleware = new Collection();
        $this->path = new Collection();
    }

    public function addMiddleware(string|array $name): static
    {
        $this->middleware->merge((array)$name);
        return $this;
    }

    public function addPath(string|array $name): static
    {
        $this->path->merge((array)$name);
        return $this;
    }


    /**
     * Summary of clean
     * unifica i valori e rimuove duplicate
     * @return static
     */
    public function clean(): static
    {
        $this->middleware->unique()->values();
        $this->path->unique()->values();
        return $this;
    }

    public function Middleware(): Collection
    {
        return $this->middleware;
    }

    public function Path(): Collection
    {
        return $this->path;
    }

  
    public function merge(Stack $stack): static
    {
        $this->middleware->merge($stack->Middleware());
        $this->path->merge($stack->Path());
      
        return $this;
    }

    public function toArray(): array{
        return [
            "Middleware" => $this->middleware->toArray(),
            "path" => $this->path->toArray(),
        ];
    }
}
