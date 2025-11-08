<?php

namespace App\Core\Eloquent\Query;

trait SqlOperation
{
    use SqlClauses;
  
    public function count(string $column = '*', string $alias = 'aggregate'): self
    {
        $agg = "COUNT({$column}) AS {$alias}";
        $this->selectValues .= ", {$agg}";
        return $this;
    }
    public function max(string $column, string $alias): self
    {
        $this->selectValues .= ", MAX({$column}) as $alias";
        return $this;
    }

    public function min(string $column, string $alias): self
    {
        $this->selectValues .= ", MIN({$column}) as $alias";
        return $this;
    }

    public function countDistinct(string $column, string $alias = 'distinct_count'): self
    {
        $this->selectValues .= ", COUNT(DISTINCT {$column}) as {$alias}";
        return $this;
    }
    public function sum(string $column, string $alias): self
    {
        $this->selectValues .= ", SUM({$column}) as $alias";
        return $this;
    }
    public function avg(string $column, string $alias): self
    {
        $this->selectValues .= ", AVG({$column}) as $alias";
        return $this;
    }

    public function avgDistinct(string $column, string $alias = 'avg_distinct'): self
    {
        $this->selectValues .= ", AVG(DISTINCT {$column}) as {$alias}";
        return $this;
    }

    public function coalesce(string $column, string $default, string $alias): self
    {
        $this->selectValues .= ", COALESCE({$column}, '{$default}') as {$alias}";
        return $this;
    }

    public function length(string $column, string $alias = 'length'): self
    {
        $this->selectValues .= ", LENGTH({$column}) as {$alias}";
        return $this;
    }

    public function upper(string $column, string $alias = 'upper'): self
    {
        $this->selectValues .= ", UPPER({$column}) as {$alias}";
        return $this;
    }

    public function lower(string $column, string $alias = 'lower'): self
    {
        $this->selectValues .= ", LOWER({$column}) as {$alias}";
        return $this;
    }

    public function concat(array $columns, string $alias = 'concat'): self
    {
        $joined = implode(", ' ', ", $columns);
        $this->selectValues .= ", CONCAT({$joined}) as {$alias}";
        return $this;
    }

    public function dateFormat(string $column, string $format = '%Y-%m-%d', string $alias = 'formatted'): self
    {
        $this->selectValues .= ", DATE_FORMAT({$column}, '{$format}') as {$alias}";
        return $this;
    }

    public function ifNull(string $column, string $default, string $alias = null): self
    {
        $alias ??= $column;
        $this->selectValues .= ", IFNULL({$column}, '{$default}') as {$alias}";
        return $this;
    }
}
