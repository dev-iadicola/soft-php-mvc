<?php

declare(strict_types=1);

namespace App\Core\Validation\Adapters;

use App\Core\Validation\Contracts\ValidatorAdapterInterface;
use App\Core\Validation\Rules\RuleInterface;
use Closure;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class IlluminateValidatorAdapter implements ValidatorAdapterInterface
{
    /** @var array<string, RuleInterface> */
    private static array $customRules = [];

    private static ?Factory $factory = null;

    private ?\Illuminate\Validation\Validator $validator = null;

    public function validate(array $data, array $rules, array $messages = []): void
    {
        $normalizedData = $this->normalizeData($data, $rules);

        $this->validator = self::factory()->make(
            $normalizedData,
            $this->normalizeRules($rules, $normalizedData),
            $messages
        );
    }

    public function fails(): bool
    {
        return $this->validator()->fails();
    }

    public function errors(): array
    {
        return $this->validator()->errors()->toArray();
    }

    public function validated(): array
    {
        return $this->validator()->validated();
    }

    public function first(string $field): ?string
    {
        $error = $this->validator()->errors()->first($field);

        return $error !== '' ? $error : null;
    }

    public function implodeError(string $separator = '<br>'): string
    {
        $errors = $this->errors();

        if ($errors === []) {
            return '';
        }

        return implode($separator, array_merge(...array_values($errors)));
    }

    public function extend(string $ruleName, RuleInterface $rule): void
    {
        self::$customRules[$ruleName] = $rule;
    }

    public function removeExtension(string $ruleName): void
    {
        unset(self::$customRules[$ruleName]);
    }

    private function validator(): \Illuminate\Validation\Validator
    {
        if ($this->validator === null) {
            throw new \RuntimeException('Validator adapter has not been initialized.');
        }

        return $this->validator;
    }

    private static function factory(): Factory
    {
        if (self::$factory instanceof Factory) {
            return self::$factory;
        }

        $loader = new ArrayLoader();
        $loader->addMessages('en', 'validation', [
            'required' => 'The :attribute is required',
            'email' => 'The :attribute field must be a valid email address.',
            'string' => 'The :attribute must be a string.',
            'min' => [
                'string' => 'The :attribute field must be at least :min characters.',
            ],
            'max' => [
                'string' => 'The :attribute field may not be greater than :max characters.',
            ],
            'numeric' => 'The :attribute field must be a numeric value.',
            'integer' => 'The :attribute field must be an integer.',
            'boolean' => 'The :attribute field must be a boolean value (true or false).',
            'in' => 'The selected :attribute is invalid.',
            'same' => 'The :attribute field must match :other.',
            'different' => 'The :attribute field must be different from :other.',
            'image' => 'The :attribute field is not image',
            'confirmed' => 'The :attribute field confirmation does not match.',
        ]);
        $translator = new Translator($loader, 'en');

        return self::$factory = new Factory($translator);
    }

    private function normalizeData(array $data, array $rules): array
    {
        foreach ($rules as $field => $fieldRules) {
            if (isset($data[$field]) && $this->isUploadedFileArray($data[$field])) {
                $data[$field] = $this->toUploadedFile($data[$field]);
            }

            foreach ($this->ruleList($fieldRules) as $rule) {
                if (
                    $rule === 'confirmed' &&
                    array_key_exists('confirmed', $data) &&
                    ! array_key_exists("{$field}_confirmation", $data)
                ) {
                    $data["{$field}_confirmation"] = $data['confirmed'];
                }
            }
        }

        return $data;
    }

    private function normalizeRules(array $rules, array $data): array
    {
        $normalized = [];

        foreach ($rules as $field => $fieldRules) {
            $items = [];

            foreach ($this->ruleList($fieldRules) as $rule) {
                if ($rule instanceof Closure) {
                    $items[] = function (string $attribute, mixed $value, Closure $fail) use ($rule, $data): void {
                        $result = $rule($value, $data);

                        if ($result !== true) {
                            $fail(is_string($result) ? $result : "The field {$attribute} is invalid.");
                        }
                    };
                    continue;
                }

                if ($rule instanceof RuleInterface) {
                    $items[] = function (string $attribute, mixed $value, Closure $fail) use ($rule): void {
                        if (! $rule->passes($attribute, $value, null)) {
                            $fail($rule->message($attribute, null));
                        }
                    };
                    continue;
                }

                if (is_string($rule)) {
                    [$ruleName, $param] = array_pad(explode(':', $rule, 2), 2, null);

                    if (isset(self::$customRules[$ruleName])) {
                        $customRule = self::$customRules[$ruleName];
                        $items[] = function (string $attribute, mixed $value, Closure $fail) use ($customRule, $param): void {
                            if (! $customRule->passes($attribute, $value, $param)) {
                                $fail($customRule->message($attribute, $param));
                            }
                        };
                        continue;
                    }
                }

                $items[] = $rule;
            }

            $normalized[$field] = $items;
        }

        return $normalized;
    }

    /**
     * @return array<int, mixed>
     */
    private function ruleList(mixed $rules): array
    {
        if (is_array($rules)) {
            return $rules;
        }

        return explode('|', (string) $rules);
    }

    private function isUploadedFileArray(mixed $value): bool
    {
        return is_array($value) && isset($value['tmp_name'], $value['name']);
    }

    private function toUploadedFile(array $file): UploadedFile
    {
        return new UploadedFile(
            $file['tmp_name'],
            $file['name'],
            $file['type'] ?? null,
            $file['error'] ?? UPLOAD_ERR_OK,
            true
        );
    }
}
