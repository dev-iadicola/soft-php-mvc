<?php

namespace App\Core\DataLayer\Query;


use App\Core\DataLayer\Query\AbstractBuilder;
use App\Core\Exception\QueryBuilderException;


/**
 * ! TESTING
 * PostgreSQL implementation of the QueryBuilder.
 * 
 *  Fully compatible with OrmEngine and Model
 *  Uses double quotes for identifiers
 *  Supports ILIKE instead of LIKE
 *  Follows SQL-92 standard for joins and conditions
 */
class PostgresQueryBuilder extends AbstractBuilder
{




    public function toInsert(): string
    {
        return $this->insertClause . " RETURNING id;";
    }

    public function toUpdate(): string
    {
        if (empty($this->table)) {
            throw new QueryBuilderException("No table defined for UPDATE");
        }

        if (empty($this->setClause)) {
            throw new QueryBuilderException("No SET clause defined for UPDATE");
        }

        return "UPDATE \"{$this->table}\" SET {$this->setClause} {$this->whereClause} RETURNING *;";
    }


    public function toDelete(): string
    {
        if (empty($this->table)) {
            throw new QueryBuilderException("DELETE error: no table defined.");
        }

        if (empty($this->whereClause)) {
            throw new QueryBuilderException("DELETE blocked: missing WHERE clause.");
        }

        return "DELETE FROM \"{$this->table}\" {$this->whereClause} RETURNING *;";
    }

   

    public function toRawSql(): string
    {
        $sql = $this->toSql();
        foreach ($this->bindings as $key => $value) {
            $v = is_numeric($value) ? $value : "'" . addslashes($value) . "'";
            $sql = str_replace($key, $v, $sql);
        }
        return $sql;
    }


    public function insert(array $insert): static
    {
        if (is_null($this->from)) {
            throw new QueryBuilderException("No table defined for INSERT");
        }

        if (empty($insert)) {
            throw new QueryBuilderException("Empty insert data");
        }

        $table = $this->table;
        $columns = array_keys($insert);
        $values = array_values($insert);

        $cols = implode(', ', array_map(fn($c) => "\"$c\"", $columns));
        $placeholders = implode(', ', array_map(fn($v) => $this->addBind($v), $values));

        $this->insertClause = "INSERT INTO $table ($cols) VALUES ($placeholders)";
        return $this;
    }






}
