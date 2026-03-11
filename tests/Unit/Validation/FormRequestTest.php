<?php

declare(strict_types=1);

use App\Core\Exception\UnauthorizedException;
use App\Core\Exception\ValidationException;
use App\Core\Http\FormRequest;
use App\Core\Http\Request;
use PHPUnit\Framework\TestCase;

class FormRequestTest extends TestCase
{
    private function makeFormRequest(array $data, array $rules, array $messages = [], bool $authorized = true): FormRequest
    {
        // Create a Request mock with custom attributes
        $request = $this->createMock(Request::class);
        $request->method('all')->willReturn($data);

        return new class($request, $rules, $messages, $authorized) extends FormRequest {
            private array $testRules;

            private array $testMessages;

            private bool $testAuthorized;

            public function __construct(Request $request, array $rules, array $messages, bool $authorized)
            {
                parent::__construct($request);
                $this->testRules = $rules;
                $this->testMessages = $messages;
                $this->testAuthorized = $authorized;
            }

            public function rules(): array
            {
                return $this->testRules;
            }

            public function messages(): array
            {
                return $this->testMessages;
            }

            public function authorize(): bool
            {
                return $this->testAuthorized;
            }
        };
    }

    // ------------------------------------------------------------------
    //  Successful validation
    // ------------------------------------------------------------------

    public function testValidateReturnsValidatedData(): void
    {
        $formRequest = $this->makeFormRequest(
            ['name' => 'John', 'email' => 'john@example.com'],
            ['name' => 'required|string', 'email' => 'required|email']
        );

        $validated = $formRequest->validate();

        $this->assertSame('John', $validated['name']);
        $this->assertSame('john@example.com', $validated['email']);
    }

    // ------------------------------------------------------------------
    //  Validation failure
    // ------------------------------------------------------------------

    public function testValidateThrowsValidationException(): void
    {
        $formRequest = $this->makeFormRequest(
            ['name' => '', 'email' => 'not-valid'],
            ['name' => 'required', 'email' => 'required|email']
        );

        $this->expectException(ValidationException::class);
        $formRequest->validate();
    }

    public function testValidationExceptionContainsErrors(): void
    {
        $formRequest = $this->makeFormRequest(
            ['name' => ''],
            ['name' => 'required']
        );

        try {
            $formRequest->validate();
            $this->fail('Expected ValidationException was not thrown.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('name', $e->getErrors());
        }
    }

    // ------------------------------------------------------------------
    //  Authorization failure
    // ------------------------------------------------------------------

    public function testUnauthorizedThrowsException(): void
    {
        $formRequest = $this->makeFormRequest(
            ['name' => 'John'],
            ['name' => 'required'],
            [],
            false
        );

        $this->expectException(UnauthorizedException::class);
        $formRequest->validate();
    }

    // ------------------------------------------------------------------
    //  Custom messages
    // ------------------------------------------------------------------

    public function testCustomMessagesAreUsed(): void
    {
        $formRequest = $this->makeFormRequest(
            ['name' => ''],
            ['name' => 'required'],
            ['name.required' => 'Name is mandatory.']
        );

        try {
            $formRequest->validate();
            $this->fail('Expected ValidationException was not thrown.');
        } catch (ValidationException $e) {
            $errors = $e->getErrors();
            $this->assertSame('Name is mandatory.', $errors['name'][0]);
        }
    }

    // ------------------------------------------------------------------
    //  errors() method
    // ------------------------------------------------------------------

    public function testErrorsEmptyBeforeValidation(): void
    {
        $formRequest = $this->makeFormRequest(
            ['name' => 'John'],
            ['name' => 'required']
        );

        $this->assertSame([], $formRequest->errors());
    }

    public function testErrorsAvailableAfterFailedValidation(): void
    {
        $formRequest = $this->makeFormRequest(
            ['name' => ''],
            ['name' => 'required']
        );

        try {
            $formRequest->validate();
        } catch (ValidationException) {
            // expected
        }

        $this->assertArrayHasKey('name', $formRequest->errors());
    }
}
