<?php

namespace App\Core\Validation;

class Validator
{

    protected array $errors = [];

    public function __construct(protected array $data, protected array $rules, protected array $messages = [])
    {
        $this->validate();
    }

    // * Alias statico 
    public static function make(array $data, array $rules, array $messages = []): self
    {
        return new self($data, $rules, $messages);
    }

    protected function addError(string $field, string $defaultMessage, ?string $rule = null): void
    {
        $key = $rule ? "{$field}.{$rule}" : $field;

        $message = $this->messages[$key] ?? $defaultMessage;
        $this->errors[$field][] = $message;
    }


    protected function validate(): void
    {
        foreach ($this->rules as $field => $rules) {
            $value = $this->data[$field] ?? null;

            // se non è un array, rendilo con | per dividerlo
            if (!is_array($rules))
                $rules = explode('|', $rules);

            foreach ($rules as $rule) {
                // regole closure personalizzate 
                if ($rule instanceof \Closure) {
                    $result = $rule($value, $this->data);
                    if ($result !== true) {
                        $this->addError($field, $result ?? "The field $field is invalid.");
                    }
                    continue;
                }
                // * Gestione parametri tipo min:8
                [$ruleName, $param] = array_pad(explode(':', $rule, 2), 2, null);
                // * Se l'utente si crea dei mini rules, può farlo liberamente basta che abbia implementato RuleInterface
                /**
                 * $validator = Validator::make($_POST, [
                 *       'username' => [new MiniRuleCreatoDalDeveloper()],
                 *          ]);
                 */
                if ($rule instanceof \App\Core\Validation\Rules\RuleInterface) {
                    if (!$rule->passes($field, $value, $param)) {
                        $this->addError($field, $rule->message($field, $param));
                    }
                }

                // * magic method for call the validator method by cases
                $method = 'validate' . ucfirst($ruleName);
                if (method_exists($this, $method)) {
                    $this->$method($field, $value, $param);
                } else {
                    throw new \Exception("Validation rule '{$ruleName}' not implemented.");
                }
            }
        }
    }


    /**
     * Regole di validaizone
     */

    /**
     * Validation Rules - Default English messages
     */

    protected function validateRequired(string $field, $value, $param): void
    {
        if (!isset($this->data[$field]) || $value === null || $value === '' || (is_array($value) && empty($value))) {
            $this->addError($field, "The {$field} field is required.", 'required');
        }
    }

    protected function validateEmail(string $field, $value, $param): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, "The {$field} field must be a valid email address.", 'email');
        }
    }

    // * Confirmed: when the input is equal with orher input with name "confirmed"
    protected function validateConfimed(string $field, $value, $param)
    {
        return ($field === $this->data['confirmed']) ? true : false;
    }

    protected function validateMin(string $field, $value, $param): void
    {
        if (is_string($value) && strlen($value) < (int)$param) {
            $this->addError($field, "The {$field} field must be at least {$param} characters.", 'min');
        }
    }

    protected function validateMax(string $field, $value, $param): void
    {
        if (is_string($value) && strlen($value) > (int)$param) {
            $this->addError($field, "The {$field} field may not be greater than {$param} characters.", 'max');
        }
    }

    protected function validateNumeric(string $field, $value): void
    {
        if (!is_numeric($value)) {
            $this->addError($field, "The {$field} field must be a numeric value.", 'numeric');
        }
    }

    protected function validateAlpha(string $field, $value): void
    {
        if (!preg_match('/^[a-zA-Z]+$/', $value)) {
            $this->addError($field, "The {$field} field may only contain letters.", 'alpha');
        }
    }

    protected function validateAlphaNum(string $field, $value): void
    {
        if (!preg_match('/^[a-zA-Z0-9]+$/', $value)) {
            $this->addError($field, "The {$field} field may only contain letters and numbers.", 'alpha_num');
        }
    }

    protected function validateRegex(string $field, $value, $param): void
    {
        if (!preg_match($param, $value)) {
            $this->addError($field, "The {$field} field format is invalid.", 'regex');
        }
    }

    protected function validateSame(string $field, $value, $param): void
    {
        $other = $this->data[$param] ?? null;
        if ($value !== $other) {
            $this->addError($field, "The {$field} field must match {$param}.", 'same');
        }
    }

    protected function validateDifferent(string $field, $value, $param): void
    {
        $other = $this->data[$param] ?? null;
        if ($value === $other) {
            $this->addError($field, "The {$field} field must be different from {$param}.", 'different');
        }
    }

    protected function validateDate(string $field, $value): void
    {
        if (!strtotime($value)) {
            $this->addError($field, "The {$field} field must be a valid date.", 'date');
        }
    }

    protected function validateUrl(string $field, $value): void
    {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $this->addError($field, "The {$field} field must be a valid URL.", 'url');
        }
    }

    protected function validateIp(string $field, $value): void
    {
        if (!filter_var($value, FILTER_VALIDATE_IP)) {
            $this->addError($field, "The {$field} field must be a valid IP address.", 'ip');
        }
    }

    protected function validateFile(string $field, $value): void
    {
        if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
            $this->addError($field, "The file {$field} was not uploaded correctly.", 'file');
        }
    }

    protected function validateMimes(string $field, $value, $param): void
    {
        if (!isset($_FILES[$field]['name'])) {
            return;
        }
        $allowed = explode(',', $param);
        $ext = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
        if (!in_array(strtolower($ext), $allowed)) {
            $this->addError($field, "The file {$field} must be one of the following types: " . implode(', ', $allowed), 'mimes');
        }
    }

    protected function validateUppercase(string $field, $value): void
    {
        if (!preg_match('/[A-Z]/', $value)) {
            $this->addError($field, "The {$field} field must contain at least one uppercase letter.", 'uppercase');
        }
    }

    protected function validateNumber(string $field, $value): void
    {
        if (!preg_match('/[0-9]/', $value)) {
            $this->addError($field, "The {$field} field must contain at least one number.", 'number');
        }
    }

    protected function validateSymbol(string $field, $value): void
    {
        if (!preg_match('/[\W_]/', $value)) {
            $this->addError($field, "The {$field} field must contain at least one special character.", 'symbol');
        }
    }

    protected function validateIn(string $field, $value, $param): void
    {
        $allowed = explode(',', $param);
        if (!in_array($value, $allowed)) {
            $this->addError($field, "The {$field} field must be one of the following: " . implode(', ', $allowed), 'in');
        }
    }

    protected function validateNotIn(string $field, $value, $param): void
    {
        $disallowed = explode(',', $param);
        if (in_array($value, $disallowed)) {
            $this->addError($field, "The {$field} field may not be one of the following: " . implode(', ', $disallowed), 'not_in');
        }
    }

    protected function validateSize(string $field, $value, $param): void
    {
        if (strlen($value) !== (int)$param) {
            $this->addError($field, "The {$field} field must be exactly {$param} characters long.", 'size');
        }
    }

    protected function validateBoolean(string $field, $value): void
    {
        $accepted = [true, false, 1, 0, '1', '0', 'true', 'false', 'yes', 'no'];
        if (!in_array($value, $accepted, true)) {
            $this->addError($field, "The {$field} field must be a boolean value (true or false).", 'boolean');
        }
    }

    protected function validateInteger(string $field, $value): void
    {
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            $this->addError($field, "The {$field} field must be an integer.", 'integer');
        }
    }

    protected function validateDecimal(string $field, $value): void
    {
        if (filter_var($value, FILTER_VALIDATE_FLOAT) === false) {
            $this->addError($field, "The {$field} field must be a decimal number.", 'decimal');
        }
    }

    protected function validateBetween(string $field, $value, $param): void
    {
        [$min, $max] = explode(',', $param);
        if ($value < $min || $value > $max) {
            $this->addError($field, "The {$field} field must be between {$min} and {$max}.", 'between');
        }
    }

    protected function validateArrayMin(string $field, $value, $param): void
    {
        if (is_array($value) && count($value) < (int)$param) {
            $this->addError($field, "The {$field} field must have at least {$param} items.", 'array_min');
        }
    }

    protected function validateArrayMax(string $field, $value, $param): void
    {
        if (is_array($value) && count($value) > (int)$param) {
            $this->addError($field, "The {$field} field may not have more than {$param} items.", 'array_max');
        }
    }


    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function first(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }
}
