<?php

declare(strict_types=1);

namespace App\Core\DataLayer\Schema;

class Column {
    

private array $columns = [];

// Field =>id
// Type =>int
// Null =>NO
// Key =>PRI
// Default =>
// Extra =>auto_increment
    public function __construct(
    protected string $nameColumn, 
    protected string $type, 
    protected bool $nullable = false, 
    protected string|int|float|bool|null $default = null,
    protected string $extra = '') {

        $this->columns[$nameColumn] = array(
            'Type'=> $type,
            'Default'=> $default,
            'Null'=> $nullable,
        );
    }



    private function CheckArrayKey(): void{

    }


}
