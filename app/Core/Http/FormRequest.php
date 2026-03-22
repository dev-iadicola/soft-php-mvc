<?php

declare(strict_types=1);

namespace App\Core\Http;

use App\Core\Exception\UnauthorizedException;
use App\Core\Exception\ValidationException;
use App\Core\Validation\Validator;

abstract class FormRequest
{
    protected Request $request;

    protected Validator $validator;

    public function __construct(?Request $request = null)
    {
        $this->request = $request ?? new Request();
    }

    /**
     * Define the validation rules for this form request.
     *
     * @return array<string, mixed>
     */
    abstract public function rules(): array;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * Validate the request data against the defined rules.
     *
     * @return array<string, mixed> The validated data
     *
     * @throws UnauthorizedException If authorization fails
     * @throws ValidationException   If validation fails
     */
    public function validate(): array
    {
        if (! $this->authorize()) {
            throw new UnauthorizedException('This action is unauthorized.');
        }

        $this->validator = Validator::make(
            $this->data(),
            $this->rules(),
            $this->messages()
        );

        if ($this->validator->fails()) {
            throw new ValidationException(
                $this->validator->errors(),
                'Validation failed'
            );
        }

        return $this->validator->validated();
    }

    /**
     * Get the data to be validated.
     *
     * @return array<string, mixed>
     */
    public function data(): array
    {
        return $this->request->all();
    }

    /**
     * Get the validation errors (available after validate() is called).
     *
     * @return array<string, string[]>
     */
    public function errors(): array
    {
        return isset($this->validator) ? $this->validator->errors() : [];
    }

    /**
     * Get the underlying Request instance.
     */
    public function request(): Request
    {
        return $this->request;
    }
}
