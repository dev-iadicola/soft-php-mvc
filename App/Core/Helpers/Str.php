<?php

declare(strict_types=1);

namespace App\Core\Helpers;

/**
 * Immutable string utility class.
 *
 * Every method is a pure function (no side-effects) that operates on the
 * provided value and returns a new string or scalar.
 *
 * The class is **final** with a private constructor so it cannot be
 * instantiated or extended.  Follow the Open/Closed Principle by composing
 * this class inside your own helpers rather than inheriting from it.
 */
final class Str
{
    /** Prevent instantiation. */
    private function __construct() {}

    // ------------------------------------------------------------------
    //  Case conversion
    // ------------------------------------------------------------------

    /**
     * Convert the given string to lower-case (UTF-8 safe).
     */
    public static function lower(string $value): string
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * Convert the given string to upper-case (UTF-8 safe).
     */
    public static function upper(string $value): string
    {
        return mb_strtoupper($value, 'UTF-8');
    }

    /**
     * Convert the given string to Title Case.
     */
    public static function title(string $value): string
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Convert the given string to camelCase.
     */
    public static function camel(string $value): string
    {
        return self::lcfirst(self::studly($value));
    }

    /**
     * Convert the given string to snake_case.
     */
    public static function snake(string $value, string $delimiter = '_'): string
    {
        // Insert the delimiter before every upper-case letter that follows a
        // lower-case letter or digit, or before a sequence of upper-case
        // letters followed by a lower-case letter (e.g. "HTMLParser" -> "html_parser").
        $value = preg_replace('/([a-z\d])([A-Z])/', '$1' . $delimiter . '$2', $value);
        $value = preg_replace('/([A-Z]+)([A-Z][a-z])/', '$1' . $delimiter . '$2', (string) $value);

        // Replace any non-alphanumeric characters with the delimiter.
        $value = preg_replace('/[^a-zA-Z\d]+/', $delimiter, (string) $value);

        return self::lower(trim((string) $value, $delimiter));
    }

    /**
     * Convert the given string to kebab-case.
     */
    public static function kebab(string $value): string
    {
        return self::snake($value, '-');
    }

    /**
     * Convert the given string to StudlyCase (PascalCase).
     */
    public static function studly(string $value): string
    {
        $words = preg_split('/[-_\s]+/', $value);

        if ($words === false) {
            return $value;
        }

        $studly = '';
        foreach ($words as $word) {
            $studly .= self::ucfirst($word);
        }

        return $studly;
    }

    /**
     * Generate a URL-friendly slug from the given string.
     */
    public static function slug(string $value, string $separator = '-'): string
    {
        // Transliterate to ASCII when possible.
        if (function_exists('transliterator_transliterate')) {
            $value = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $value) ?: $value;
        } else {
            $value = self::lower($value);
        }

        // Replace non-alphanumeric characters with the separator.
        $value = preg_replace('/[^a-z\d]+/i', $separator, $value);

        // Collapse consecutive separators and trim.
        $value = preg_replace('/' . preg_quote($separator, '/') . '+/', $separator, (string) $value);

        return trim(self::lower((string) $value), $separator);
    }

    // ------------------------------------------------------------------
    //  Inspection helpers
    // ------------------------------------------------------------------

    /**
     * Determine if the given string contains a given substring (or any of the given substrings).
     */
    public static function contains(string $haystack, string|array $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the given string starts with a given substring (or any of the given substrings).
     */
    public static function startsWith(string $haystack, string|array $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && str_starts_with($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the given string ends with a given substring (or any of the given substrings).
     */
    public static function endsWith(string $haystack, string|array $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && str_ends_with($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return the length of the given string (UTF-8 safe).
     */
    public static function length(string $value): int
    {
        return mb_strlen($value, 'UTF-8');
    }

    /**
     * Return the number of words in the given string.
     */
    public static function wordCount(string $value): int
    {
        return str_word_count($value);
    }

    /**
     * Determine if the given string is empty (after trimming).
     */
    public static function isEmpty(string $value): bool
    {
        return trim($value) === '';
    }

    /**
     * Determine if the given string is NOT empty (after trimming).
     */
    public static function isNotEmpty(string $value): bool
    {
        return ! self::isEmpty($value);
    }

    /**
     * Determine if the given string matches a wildcard pattern.
     *
     * Asterisks are translated into zero-or-more regex wildcards.
     */
    public static function is(string $pattern, string $value): bool
    {
        if ($pattern === $value) {
            return true;
        }

        $pattern = preg_quote($pattern, '#');

        // Replace escaped asterisks with a regex wildcard.
        $pattern = str_replace('\*', '.*', $pattern);

        return (bool) preg_match('#^' . $pattern . '\z#u', $value);
    }

    // ------------------------------------------------------------------
    //  Truncation
    // ------------------------------------------------------------------

    /**
     * Limit the number of characters in the given string.
     */
    public static function limit(string $value, int $limit = 100, string $end = '...'): string
    {
        if (mb_strlen($value, 'UTF-8') <= $limit) {
            return $value;
        }

        return mb_substr($value, 0, $limit, 'UTF-8') . $end;
    }

    /**
     * Limit the number of words in the given string.
     */
    public static function words(string $value, int $words = 100, string $end = '...'): string
    {
        preg_match('/^\s*(?:\S+\s*){1,' . $words . '}/u', $value, $matches);

        if (! isset($matches[0]) || mb_strlen($value, 'UTF-8') === mb_strlen(trim($matches[0]), 'UTF-8')) {
            return $value;
        }

        return trim($matches[0]) . $end;
    }

    // ------------------------------------------------------------------
    //  Sub-string extraction
    // ------------------------------------------------------------------

    /**
     * Return the portion of the string after the first occurrence of the given search value.
     */
    public static function after(string $subject, string $search): string
    {
        if ($search === '') {
            return $subject;
        }

        $pos = mb_strpos($subject, $search, 0, 'UTF-8');

        if ($pos === false) {
            return $subject;
        }

        return mb_substr($subject, $pos + mb_strlen($search, 'UTF-8'), null, 'UTF-8');
    }

    /**
     * Return the portion of the string before the first occurrence of the given search value.
     */
    public static function before(string $subject, string $search): string
    {
        if ($search === '') {
            return $subject;
        }

        $pos = mb_strpos($subject, $search, 0, 'UTF-8');

        if ($pos === false) {
            return $subject;
        }

        return mb_substr($subject, 0, $pos, 'UTF-8');
    }

    /**
     * Return the portion of the string between the first occurrence of *from* and
     * the first occurrence of *to* that follows it.
     */
    public static function between(string $subject, string $from, string $to): string
    {
        if ($from === '' || $to === '') {
            return $subject;
        }

        return self::before(self::after($subject, $from), $to);
    }

    // ------------------------------------------------------------------
    //  Replacement
    // ------------------------------------------------------------------

    /**
     * Replace occurrences of search value(s) in the subject.
     */
    public static function replace(string|array $search, string|array $replace, string $subject): string
    {
        return str_replace($search, $replace, $subject);
    }

    /**
     * Replace the first occurrence of the search value in the subject.
     */
    public static function replaceFirst(string $search, string $replace, string $subject): string
    {
        if ($search === '') {
            return $subject;
        }

        $pos = strpos($subject, $search);

        if ($pos === false) {
            return $subject;
        }

        return substr_replace($subject, $replace, $pos, strlen($search));
    }

    /**
     * Replace the last occurrence of the search value in the subject.
     */
    public static function replaceLast(string $search, string $replace, string $subject): string
    {
        if ($search === '') {
            return $subject;
        }

        $pos = strrpos($subject, $search);

        if ($pos === false) {
            return $subject;
        }

        return substr_replace($subject, $replace, $pos, strlen($search));
    }

    // ------------------------------------------------------------------
    //  Casing helpers (single-character)
    // ------------------------------------------------------------------

    /**
     * Make the first character of the given string upper-case (UTF-8 safe).
     */
    public static function ucfirst(string $value): string
    {
        return mb_strtoupper(mb_substr($value, 0, 1, 'UTF-8'), 'UTF-8')
             . mb_substr($value, 1, null, 'UTF-8');
    }

    /**
     * Make the first character of the given string lower-case (UTF-8 safe).
     */
    public static function lcfirst(string $value): string
    {
        return mb_strtolower(mb_substr($value, 0, 1, 'UTF-8'), 'UTF-8')
             . mb_substr($value, 1, null, 'UTF-8');
    }

    // ------------------------------------------------------------------
    //  Pluralisation / Singularisation (basic English rules)
    // ------------------------------------------------------------------

    /**
     * Get the basic English plural form of the given word.
     *
     * This is intentionally simple; for full i18n support consider a
     * dedicated inflector package.
     */
    public static function plural(string $value): string
    {
        if ($value === '') {
            return $value;
        }

        $lower = self::lower($value);

        // Uncountable words.
        $uncountable = [
            'audio', 'bison', 'cattle', 'chassis', 'compensation', 'coreopsis',
            'data', 'deer', 'education', 'emoji', 'equipment', 'evidence',
            'feedback', 'firmware', 'fish', 'furniture', 'gold', 'hardware',
            'information', 'jedi', 'knowledge', 'legislation', 'livestock',
            'mathematics', 'metadata', 'moose', 'music', 'news', 'nutrition',
            'offspring', 'plankton', 'pokemon', 'police', 'rain', 'rice',
            'series', 'sheep', 'software', 'species', 'swine', 'traffic',
            'wheat',
        ];

        if (in_array($lower, $uncountable, true)) {
            return $value;
        }

        // Irregular words.
        $irregulars = [
            'child'  => 'children',
            'goose'  => 'geese',
            'man'    => 'men',
            'woman'  => 'women',
            'tooth'  => 'teeth',
            'foot'   => 'feet',
            'mouse'  => 'mice',
            'person' => 'people',
            'ox'     => 'oxen',
        ];

        if (isset($irregulars[$lower])) {
            return $irregulars[$lower];
        }

        // Regular rules (order matters).
        if (str_ends_with($lower, 'y') && ! in_array($lower[-2] ?? '', ['a', 'e', 'i', 'o', 'u'])) {
            return substr($value, 0, -1) . 'ies';
        }

        if (str_ends_with($lower, 's') || str_ends_with($lower, 'x')
            || str_ends_with($lower, 'sh') || str_ends_with($lower, 'ch')) {
            return $value . 'es';
        }

        if (str_ends_with($lower, 'fe')) {
            return substr($value, 0, -2) . 'ves';
        }

        if (str_ends_with($lower, 'f')) {
            return substr($value, 0, -1) . 'ves';
        }

        return $value . 's';
    }

    /**
     * Get the basic English singular form of the given word.
     *
     * This is intentionally simple; for full i18n support consider a
     * dedicated inflector package.
     */
    public static function singular(string $value): string
    {
        if ($value === '') {
            return $value;
        }

        $lower = self::lower($value);

        // Uncountable words.
        $uncountable = [
            'audio', 'bison', 'cattle', 'chassis', 'compensation', 'coreopsis',
            'data', 'deer', 'education', 'emoji', 'equipment', 'evidence',
            'feedback', 'firmware', 'fish', 'furniture', 'gold', 'hardware',
            'information', 'jedi', 'knowledge', 'legislation', 'livestock',
            'mathematics', 'metadata', 'moose', 'music', 'news', 'nutrition',
            'offspring', 'plankton', 'pokemon', 'police', 'rain', 'rice',
            'series', 'sheep', 'software', 'species', 'swine', 'traffic',
            'wheat',
        ];

        if (in_array($lower, $uncountable, true)) {
            return $value;
        }

        // Irregular words (reversed).
        $irregulars = [
            'children' => 'child',
            'geese'    => 'goose',
            'men'      => 'man',
            'women'    => 'woman',
            'teeth'    => 'tooth',
            'feet'     => 'foot',
            'mice'     => 'mouse',
            'people'   => 'person',
            'oxen'     => 'ox',
        ];

        if (isset($irregulars[$lower])) {
            return $irregulars[$lower];
        }

        // Regular rules (order matters).
        if (str_ends_with($lower, 'ies')) {
            return substr($value, 0, -3) . 'y';
        }

        if (str_ends_with($lower, 'ves')) {
            return substr($value, 0, -3) . 'f';
        }

        if (str_ends_with($lower, 'ses') || str_ends_with($lower, 'xes')
            || str_ends_with($lower, 'shes') || str_ends_with($lower, 'ches')) {
            return substr($value, 0, -2);
        }

        if (str_ends_with($lower, 's') && ! str_ends_with($lower, 'ss')) {
            return substr($value, 0, -1);
        }

        return $value;
    }

    // ------------------------------------------------------------------
    //  Random string generation
    // ------------------------------------------------------------------

    /**
     * Generate a random alpha-numeric string of the given length.
     */
    public static function random(int $length = 16): string
    {
        $bytes = random_bytes((int) ceil($length / 2));

        return substr(bin2hex($bytes), 0, $length);
    }
}
