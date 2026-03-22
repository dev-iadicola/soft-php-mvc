<?php

declare(strict_types=1);

namespace App\Utils;

use DateTimeImmutable;
use Exception;

class Casting
{
    /**
     * Esegue il casting automatico dei valori di un array.
     * Supporta int, float, bool, null, date (parzialmente), e array JSON.
     */
    public static function formatArray(array $array): array
    {
        $newArray = [];

        foreach ($array as $key => $val) {

            $newArray[$key] = static::format($val);
        }

        return $newArray;
    }

    public static function format(mixed $val)
    {

        // if is string return imedialtry original value
        if ( ! is_string($val)) {
            return $val;
        }

        $trimmed = trim($val);

        // Booleani
        if (in_array(strtolower($trimmed), ['true', 'false', 'yes', 'no', 'on', 'off'], true)) {
            $val = in_array(strtolower($trimmed), ['true', 'yes', 'on'], true);
        }

        // Null
        elseif (strtolower($trimmed) === 'null' || $trimmed === '') {
            $val = null;
        }

        //  integer
        elseif (ctype_digit($trimmed)) {
            $val = (int) $trimmed;
        }

        // decimal  number decimali
        elseif (is_numeric($trimmed)) {
            $val = (float) $trimmed;
        }

        // JSON (array or object)
        elseif ($thisVal = json_decode($trimmed, true)) {
            if (json_last_error() === JSON_ERROR_NONE) {
                $val = $thisVal;
            }
        }

        //  Date ISO
        elseif (preg_match('/^\d{4}-\d{2}-\d{2}/', $trimmed)) {
            try {
                $val = new DateTimeImmutable($trimmed);
            } catch (Exception $e) {
                // Non valido → lascia come stringa
            }
        }

    }

    public static function cast(mixed $value, string $type): mixed
    {
        return match ($type) {
            'int'    => (int) $value,
            'float'  => (float) $value,
            'bool'   => filter_var($value, FILTER_VALIDATE_BOOL),
            'string' => (string) $value,
            default  => $value,
        };
    }

}
