<?php
namespace App\Core;

class Component {

    private array $items = [];
    private string $name;

    public function __construct(?string $componentName = '') {
        $this->name = $componentName;
    }

    public function setItem($item) {
        array_push($this->items, $item);
    }

    public function setItems($items) {
        $this->items = $items;
    }

    public function getItems() {
        return $this->items;
    }

    public function getName() {
        return $this->name;
    }

}