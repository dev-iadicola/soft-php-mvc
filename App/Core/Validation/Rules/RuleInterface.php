<?php
namespace App\Core\Validation\Rules;

interface RuleInterface
{
    /**
     * Determina se il valore passa la validazione
     */
    public function passes(string $field, $value, ?string $param = null): bool;

    /**
     * Ritorna il messaggio di errore in caso di fallimento
     */
    public function message(string $field, ?string $param = null): string;
}
