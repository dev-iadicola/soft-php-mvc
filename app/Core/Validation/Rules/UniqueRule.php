<?php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

use App\Core\Database;
use PDO;

/**
 * Validates that a value is unique in a given database table.
 *
 * Usage in rules array:
 *   - As object:  new UniqueRule('users', 'email')
 *   - As string (after registering): 'unique:users,email' or 'unique:users,email,5'
 *     where 5 is the ID to exclude (useful for updates).
 *
 * Registration example:
 *   Validator::extend('unique', new UniqueRule());
 */
class UniqueRule implements RuleInterface
{
    private string $table;

    private string $column;

    private ?int $excludeId;

    /**
     * @param string   $table     The database table name
     * @param string   $column    The column to check for uniqueness
     * @param int|null $excludeId An optional ID to exclude from the check (for updates)
     */
    public function __construct(string $table = '', string $column = '', ?int $excludeId = null)
    {
        $this->table = $table;
        $this->column = $column;
        $this->excludeId = $excludeId;
    }

    /**
     * @param string      $field The field name being validated
     * @param mixed       $value The value to check
     * @param string|null $param Colon-separated params: "table,column" or "table,column,excludeId"
     */
    public function passes(string $field, mixed $value, ?string $param = null): bool
    {
        $table = $this->table;
        $column = $this->column;
        $excludeId = $this->excludeId;

        // Parse params from string notation: "table,column,excludeId"
        if ($param !== null) {
            $parts = explode(',', $param);
            $table = $parts[0] ?? $table;
            $column = $parts[1] ?? ($column !== '' ? $column : $field);
            $excludeId = isset($parts[2]) ? (int) $parts[2] : $excludeId;
        }

        // Default column to the field name if still empty
        if ($column === '') {
            $column = $field;
        }

        $pdo = Database::getInstance()->getConnection();

        $sql = "SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = :value";
        $bindings = ['value' => $value];

        if ($excludeId !== null) {
            $sql .= " AND `id` != :exclude_id";
            $bindings['exclude_id'] = $excludeId;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($bindings);

        return (int) $stmt->fetchColumn() === 0;
    }

    public function message(string $field, ?string $param = null): string
    {
        return "The {$field} has already been taken.";
    }
}
