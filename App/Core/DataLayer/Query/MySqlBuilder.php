<?php

namespace App\Core\DataLayer\Query;

use App\Core\DataLayer\Query\AbstractBuilder;
use App\Core\Exception\QueryBuilderException;
use App\Core\Helpers\Log;

class MySqlBuilder extends AbstractBuilder
{


    // * 
    #region TO STRING 
    // * ___________________________________________________


    /**
     * Summary of toUpdate
     * return a stirng with query for update record
     * @return string
     */
    public function toUpdate(): string
    {
        return "UPDATE {$this->table} SET {$this->setClause} WHERE {$this->whereClause}";
    }

    public function toInsert(): string
    {
        return $this->insertClause;
    }
    public function toRawSql(): string
    {
        $sql = $this->toSql();
        foreach ($this->bindings as $key => $val) {
            $val = is_numeric($val) ? $val : "'$val'";
            $sql = str_replace($key, $val, $sql);
        }
        return $sql;
    }



    #region INSERT

   
    public function insert(array $values): static
    {
        $filteredValues = $this->fill($values);
        if (empty($filteredValues)) {
            throw new \InvalidArgumentException("Impossible enter any value.");
        }
        $this->bindings = [];
        $columns = [];
        $placeholders = [];
        if (property_exists($this, 'timestamps') && $this->timestamps === true) {
            $now = date('Y-m-d H:i:s');
            $filteredValues['created_at'] = $filteredValues['created_at'] ?? $now;
            $filteredValues['updated_at'] = $filteredValues['updated_at'] ?? $now;
        }

        // preprare binding
        foreach ($filteredValues as $column => $val) {
        
            // add safe column name
            $columns[] = $column;
            $placeholders[] = ':' . $column; 
            $this->bindings[':' . $column] = $val;

        }

        // prepare columns and placeholder for Query
        $this->insertClause =
            "INSERT INTO {$this->table} (" .
            implode(', ', $columns) .
            ") VALUES (" .
            implode(', ', $placeholders) .
            ")";
        return $this;
    }


}
