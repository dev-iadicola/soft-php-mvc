<?php

declare(strict_types=1);

namespace App\Core\Validation;

use App\Core\Validation\Adapters\IlluminateValidatorAdapter;
use App\Core\Validation\Contracts\ValidatorAdapterInterface;
use App\Core\Validation\Rules\RuleInterface;

class Validator
{
    private static ?ValidatorAdapterInterface $adapterPrototype = null;

    private ValidatorAdapterInterface $adapter;

    public function __construct(
        protected array $data,
        protected array $rules,
        protected array $messages = []
    ) {
        $this->adapter = clone self::adapterPrototype();
        $this->adapter->validate($data, $rules, $messages);
    }

    public static function extend(string $ruleName, RuleInterface $rule): void
    {
        self::adapterPrototype()->extend($ruleName, $rule);
    }

    public static function removeExtension(string $ruleName): void
    {
        self::adapterPrototype()->removeExtension($ruleName);
    }

    public static function useAdapter(ValidatorAdapterInterface $adapter): void
    {
        self::$adapterPrototype = $adapter;
    }

    public static function make(array $data, array $rules, array $messages = []): self
    {
        return new self($data, $rules, $messages);
    }

    public function validated(): array
    {
        return $this->adapter->validated();
    }

    public function fails(): bool
    {
        return $this->adapter->fails();
    }

    public function errors(): array
    {
        return $this->adapter->errors();
    }

    public function implodeError(?string $separator = '<br>'): string
    {
        return $this->adapter->implodeError($separator ?? '<br>');
    }

    public function first(string $field): ?string
    {
        return $this->adapter->first($field);
    }

    private static function adapterPrototype(): ValidatorAdapterInterface
    {
        if (self::$adapterPrototype instanceof ValidatorAdapterInterface) {
            return self::$adapterPrototype;
        }

        return self::$adapterPrototype = new IlluminateValidatorAdapter();
    }
}
