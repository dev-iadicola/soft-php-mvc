<?php

namespace App\Core\Eloquent\Query;

trait SqlOperation
{
    use SqlClauses;
    use Execute;   
    public function count(string $column = '*'):int|false
    {
        $this->selectValues = " COUNT({$column}) AS count";
        return $this->fetchColumn();
    }
    
    public function max(string $column): int|false
    {
        $this->selectValues = " MAX({$column})";
        return $this->fetchColumn();
    }

    public function min(string $column ): int|false
    {
        $this->selectValues = " MIN({$column})";
        return $this->fetchColumn();
    }

    public function countDistinct(string $column): int|false
    {
        $this->selectValues = " COUNT(DISTINCT {$column}) ";
        return $this->fetchColumn();
    }
    public function sum(string $column ): int|false
    {
        $this->selectValues = " SUM({$column}) ";
        return $this->fetchColumn();
    }
    public function sumDistinct(string $column): int|false
    {
        $this->selectValues = " SUM( DISTINCT {$column}) ";
        return $this->fetchColumn();
    }
    public function avg(string $column ): int|false
    {
        $this->selectValues = " AVG({$column}) ";
        return $this->fetchColumn();
    }

    public function avgDistinct(string $column): int|false
    {
        $this->selectValues = " AVG(DISTINCT {$column}) ";
        return $this->fetchColumn();
    }

    // public function coalesce(string $column, string $default, ): int|false
    // {
    //     $this->selectValues .= ", COALESCE({$column}, '{$default}') as {$alias}";
    //     return $this->fetchColumn();
    // }

    // public function length(string $column): int|false
    // {
    //     $this->selectValues .= ", LENGTH({$column})";
    //     return $this->fetchColumn();
    // }

    // public function upper(string $column, string $alias = 'upper'): int|false
    // {
    //     $this->selectValues .= ", UPPER({$column}) as {$alias}";
    //     return $this->fetchColumn();
    // }

    // public function lower(string $column, string $alias = 'lower'): int|false
    // {
    //     $this->selectValues .= ", LOWER({$column}) as {$alias}";
    //     return $this->fetchColumn();
    // }

    // public function concat(array $columns, string $alias = 'concat'): int|false
    // {
    //     $joined = implode(", ' ', ", $columns);
    //     $this->selectValues .= ", CONCAT({$joined}) as {$alias}";
    //     return $this->fetchColumn();
    // }

    // public function dateFormat(string $column, string $format = '%Y-%m-%d', string $alias = 'formatted'): int|false
    // {
    //     $this->selectValues .= ", DATE_FORMAT({$column}, '{$format}') as {$alias}";
    //     return $this->fetchColumn();
    // }

    // public function ifNull(string $column, string $default): int|false
    // {
    //     $this->selectValues .= ", IFNULL({$column}, '{$default}') ";
    //     return $this->fetchColumn();
    // }
}
