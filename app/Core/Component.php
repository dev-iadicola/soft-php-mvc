<?php

declare(strict_types=1);

namespace App\Core;

class Component {

    private array $items = [];
    private string $name;

    public function __construct(?string $componentName = '') {
        $this->name = $componentName;
    }

    public function setItem(mixed $item): void {
        array_push($this->items, $item);
    }

    public function setItems(array $items): void {
        $this->items = $items;
    }

    public function getItems(): array {
        return $this->items;
    }

    public function getName(): string {
        return $this->name;
    }

}
