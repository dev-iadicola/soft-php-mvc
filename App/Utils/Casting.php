<?php 
namespace App\Utils;

class Casting {

    /**
     * Esegue il casting automatico dei valori di un array.
     * Supporta int, float, bool, null, date (parzialmente), e array JSON.
     */
    public static function formatArray(array $array): array
    {
        $newArray = [];

        foreach ($array as $key => $val) {

            // Se non è una stringa, mantieni il valore com'è
            if (!is_string($val)) {
                $newArray[$key] = $val;
                continue;
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

            //  Numeri interi
            elseif (ctype_digit($trimmed)) {
                $val = (int) $trimmed;
            }

            // Numeri decimali
            elseif (is_numeric($trimmed)) {
                $val = (float) $trimmed;
            }

            // JSON (array o oggetti)
            elseif ($thisVal = json_decode($trimmed, true)) {
                if (json_last_error() === JSON_ERROR_NONE) {
                    $val = $thisVal;
                }
            }

            //  Date ISO o comuni
            elseif (preg_match('/^\d{4}-\d{2}-\d{2}/', $trimmed)) {
                try {
                    $val = new \DateTimeImmutable($trimmed);
                } catch (\Exception $e) {
                    // Non valido → lascia come stringa
                }
            }

            $newArray[$key] = $val;
        }

        return $newArray;
    }
}
