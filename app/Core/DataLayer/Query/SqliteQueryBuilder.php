<?php

declare(strict_types=1);

namespace App\Core\DataLayer\Query;

/**
 * SQLite query builder.
 *
 * The current ORM query surface is compatible with the generic SQL generated
 * by MySqlBuilder, but SQLite still deserves an explicit dialect class so the
 * runtime does not misidentify the active builder.
 */
class SqliteQueryBuilder extends MySqlBuilder
{
}
