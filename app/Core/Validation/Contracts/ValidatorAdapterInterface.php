<?php

declare(strict_types=1);

namespace App\Core\Validation\Contracts;

use App\Core\Validation\Rules\RuleInterface;

interface ValidatorAdapterInterface
{
    public function validate(array $data, array $rules, array $messages = []): void;

    public function fails(): bool;

    public function errors(): array;

    public function validated(): array;

    public function first(string $field): ?string;

    public function implodeError(string $separator = '<br>'): string;

    public function extend(string $ruleName, RuleInterface $rule): void;

    public function removeExtension(string $ruleName): void;
}
