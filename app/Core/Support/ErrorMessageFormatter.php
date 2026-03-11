<?php

declare(strict_types=1);

namespace App\Core\Support;

class ErrorMessageFormatter
{
    public static function format(string|array $message, string $separator = ', '): string
    {
        if (is_string($message)) {
            return $message;
        }

        $normalized = [];

        foreach ($message as $value) {
            foreach (is_array($value) ? $value : [$value] as $item) {
                $normalized[] = (string) $item;
            }
        }

        return implode($separator, $normalized);
    }
}
