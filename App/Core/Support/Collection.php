<?php 
namespace App\Core\Support;

use ArrayIterator;
use Traversable;

class Collection implements \IteratorAggregate
{
    public function __construct(private array $items = []) {}

    public function all(): array
    {
        return $this->items;
    }

    public function merge(array|self $items): static
    {
        $this->items = array_merge($this->items, $items instanceof self ? $items->all() : $items);
        return $this;
    }

    public function unique(): static
    {
        $this->items = array_unique($this->items);
        return $this;
    }

    public function values(): static
    {
        $this->items = array_values($this->items);
        return $this;
    }

    public function add(mixed $item): static
    {
        $this->items[] = $item;
        return $this;
    }

    public function filter(callable $callback): static {
        return new static(array_filter($this->items, $callback));
    }
     public function map(callable $callback): static {
        return new static(array_map($callback, $this->items));
    }
    public function toArray(): array
    {
        return $this->items;
    }

     public function getIterator(): Traversable{
        return new ArrayIterator($this->items);
    }
}
