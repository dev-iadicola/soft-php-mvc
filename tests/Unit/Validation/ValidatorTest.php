<?php

declare(strict_types=1);

use App\Core\Validation\Rules\RuleInterface;
use App\Core\Validation\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    // ------------------------------------------------------------------
    //  Required
    // ------------------------------------------------------------------

    public function testRequiredPassesWithValue(): void
    {
        $v = Validator::make(['name' => 'John'], ['name' => 'required']);
        $this->assertFalse($v->fails());
        $this->assertSame(['name' => 'John'], $v->validated());
    }

    public function testRequiredFailsWhenNull(): void
    {
        $v = Validator::make([], ['name' => 'required']);
        $this->assertTrue($v->fails());
        $this->assertArrayHasKey('name', $v->errors());
    }

    public function testRequiredFailsWhenEmptyString(): void
    {
        $v = Validator::make(['name' => ''], ['name' => 'required']);
        $this->assertTrue($v->fails());
    }

    // ------------------------------------------------------------------
    //  Email
    // ------------------------------------------------------------------

    public function testEmailPasses(): void
    {
        $v = Validator::make(['email' => 'test@example.com'], ['email' => 'email']);
        $this->assertFalse($v->fails());
    }

    public function testEmailFails(): void
    {
        $v = Validator::make(['email' => 'not-an-email'], ['email' => 'email']);
        $this->assertTrue($v->fails());
    }

    // ------------------------------------------------------------------
    //  String
    // ------------------------------------------------------------------

    public function testStringPasses(): void
    {
        $v = Validator::make(['name' => 'hello'], ['name' => 'string']);
        $this->assertFalse($v->fails());
    }

    public function testStringFailsWithInteger(): void
    {
        $v = Validator::make(['name' => 123], ['name' => 'string']);
        $this->assertTrue($v->fails());
    }

    // ------------------------------------------------------------------
    //  Min / Max
    // ------------------------------------------------------------------

    public function testMinPasses(): void
    {
        $v = Validator::make(['name' => 'hello'], ['name' => 'min:3']);
        $this->assertFalse($v->fails());
    }

    public function testMinFails(): void
    {
        $v = Validator::make(['name' => 'hi'], ['name' => 'min:3']);
        $this->assertTrue($v->fails());
    }

    public function testMaxPasses(): void
    {
        $v = Validator::make(['name' => 'hi'], ['name' => 'max:5']);
        $this->assertFalse($v->fails());
    }

    public function testMaxFails(): void
    {
        $v = Validator::make(['name' => 'toolongname'], ['name' => 'max:5']);
        $this->assertTrue($v->fails());
    }

    // ------------------------------------------------------------------
    //  Numeric / Integer / Decimal
    // ------------------------------------------------------------------

    public function testNumericPasses(): void
    {
        $v = Validator::make(['age' => '25'], ['age' => 'numeric']);
        $this->assertFalse($v->fails());
    }

    public function testNumericFails(): void
    {
        $v = Validator::make(['age' => 'abc'], ['age' => 'numeric']);
        $this->assertTrue($v->fails());
    }

    public function testIntegerPasses(): void
    {
        $v = Validator::make(['count' => '42'], ['count' => 'integer']);
        $this->assertFalse($v->fails());
    }

    public function testIntegerFails(): void
    {
        $v = Validator::make(['count' => '3.14'], ['count' => 'integer']);
        $this->assertTrue($v->fails());
    }

    // ------------------------------------------------------------------
    //  Boolean
    // ------------------------------------------------------------------

    public function testBooleanPasses(): void
    {
        $v = Validator::make(['active' => '1'], ['active' => 'boolean']);
        $this->assertFalse($v->fails());
    }

    public function testBooleanFails(): void
    {
        $v = Validator::make(['active' => 'maybe'], ['active' => 'boolean']);
        $this->assertTrue($v->fails());
    }

    // ------------------------------------------------------------------
    //  In / NotIn
    // ------------------------------------------------------------------

    public function testInPasses(): void
    {
        $v = Validator::make(['color' => 'red'], ['color' => 'in:red,green,blue']);
        $this->assertFalse($v->fails());
    }

    public function testInFails(): void
    {
        $v = Validator::make(['color' => 'yellow'], ['color' => 'in:red,green,blue']);
        $this->assertTrue($v->fails());
    }

    // ------------------------------------------------------------------
    //  Same / Different
    // ------------------------------------------------------------------

    public function testSamePasses(): void
    {
        $v = Validator::make(
            ['password' => 'secret', 'password_confirm' => 'secret'],
            ['password_confirm' => 'same:password']
        );
        $this->assertFalse($v->fails());
    }

    public function testSameFails(): void
    {
        $v = Validator::make(
            ['password' => 'secret', 'password_confirm' => 'other'],
            ['password_confirm' => 'same:password']
        );
        $this->assertTrue($v->fails());
    }

    // ------------------------------------------------------------------
    //  Nullable
    // ------------------------------------------------------------------

    public function testNullableSkipsWhenEmpty(): void
    {
        $v = Validator::make(['bio' => ''], ['bio' => 'nullable|string|min:5']);
        $this->assertFalse($v->fails());
    }

    public function testNullableValidatesWhenPresent(): void
    {
        $v = Validator::make(['bio' => 'hi'], ['bio' => 'nullable|string|min:5']);
        $this->assertTrue($v->fails());
    }

    // ------------------------------------------------------------------
    //  Pipe-separated rules
    // ------------------------------------------------------------------

    public function testMultipleRulesAllPass(): void
    {
        $v = Validator::make(
            ['email' => 'john@test.com'],
            ['email' => 'required|email']
        );
        $this->assertFalse($v->fails());
    }

    public function testMultipleRulesOneFails(): void
    {
        $v = Validator::make(
            ['email' => 'not-an-email'],
            ['email' => 'required|email']
        );
        $this->assertTrue($v->fails());
    }

    // ------------------------------------------------------------------
    //  Custom messages
    // ------------------------------------------------------------------

    public function testCustomMessages(): void
    {
        $v = Validator::make(
            ['name' => ''],
            ['name' => 'required'],
            ['name.required' => 'Please provide your name.']
        );
        $this->assertTrue($v->fails());
        $this->assertSame('Please provide your name.', $v->first('name'));
    }

    // ------------------------------------------------------------------
    //  Closure rules
    // ------------------------------------------------------------------

    public function testClosureRulePasses(): void
    {
        $v = Validator::make(
            ['code' => 'ABC'],
            ['code' => [fn ($value) => $value === 'ABC' ? true : 'Invalid code.']]
        );
        $this->assertFalse($v->fails());
    }

    public function testClosureRuleFails(): void
    {
        $v = Validator::make(
            ['code' => 'XYZ'],
            ['code' => [fn ($value) => $value === 'ABC' ? true : 'Invalid code.']]
        );
        $this->assertTrue($v->fails());
        $this->assertSame('Invalid code.', $v->first('code'));
    }

    // ------------------------------------------------------------------
    //  RuleInterface inline
    // ------------------------------------------------------------------

    public function testRuleInterfaceInline(): void
    {
        $rule = new class implements RuleInterface {
            public function passes(string $field, mixed $value, ?string $param = null): bool
            {
                return $value === 'valid';
            }

            public function message(string $field, ?string $param = null): string
            {
                return "The {$field} must be 'valid'.";
            }
        };

        $v = Validator::make(['token' => 'valid'], ['token' => [$rule]]);
        $this->assertFalse($v->fails());

        $v = Validator::make(['token' => 'nope'], ['token' => [$rule]]);
        $this->assertTrue($v->fails());
    }

    // ------------------------------------------------------------------
    //  Custom rule registration (extend)
    // ------------------------------------------------------------------

    public function testExtendRegistersCustomRule(): void
    {
        $rule = new class implements RuleInterface {
            public function passes(string $field, mixed $value, ?string $param = null): bool
            {
                return str_starts_with((string) $value, 'prefix_');
            }

            public function message(string $field, ?string $param = null): string
            {
                return "The {$field} must start with 'prefix_'.";
            }
        };

        Validator::extend('starts_with_prefix', $rule);

        $v = Validator::make(['code' => 'prefix_abc'], ['code' => 'starts_with_prefix']);
        $this->assertFalse($v->fails());

        $v = Validator::make(['code' => 'nope'], ['code' => 'starts_with_prefix']);
        $this->assertTrue($v->fails());
        $this->assertSame("The code must start with 'prefix_'.", $v->first('code'));

        // Cleanup
        Validator::removeExtension('starts_with_prefix');
    }

    // ------------------------------------------------------------------
    //  validated() throws on failure
    // ------------------------------------------------------------------

    public function testValidatedThrowsWhenFails(): void
    {
        $this->expectException(\App\Core\Exception\ValidationException::class);

        $v = Validator::make([], ['name' => 'required']);
        $v->validated();
    }

    // ------------------------------------------------------------------
    //  first() and implodeError()
    // ------------------------------------------------------------------

    public function testFirstReturnsFirstError(): void
    {
        $v = Validator::make(['name' => ''], ['name' => 'required']);
        $this->assertIsString($v->first('name'));
    }

    public function testFirstReturnsNullWhenNoError(): void
    {
        $v = Validator::make(['name' => 'John'], ['name' => 'required']);
        $this->assertNull($v->first('name'));
    }

    public function testImplodeError(): void
    {
        $v = Validator::make(
            ['name' => '', 'email' => 'bad'],
            ['name' => 'required', 'email' => 'email']
        );
        $this->assertStringContainsString('<br>', $v->implodeError());
    }

    public function testImagePassesWithUploadedImageArray(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'img');
        file_put_contents(
            $tmpFile,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9p8G0iAAAAAASUVORK5CYII=')
        );

        $v = Validator::make([
            'img' => [
                'tmp_name' => $tmpFile,
                'error' => UPLOAD_ERR_OK,
                'name' => 'test.png',
            ],
        ], ['img' => 'image']);

        $this->assertFalse($v->fails());

        @unlink($tmpFile);
    }

    public function testImageFailsWithInvalidUploadedFileArray(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'txt');
        file_put_contents($tmpFile, 'not an image');

        $v = Validator::make([
            'img' => [
                'tmp_name' => $tmpFile,
                'error' => UPLOAD_ERR_OK,
                'name' => 'test.txt',
            ],
        ], ['img' => 'image']);

        $this->assertTrue($v->fails());
        $this->assertSame('The img field is not image', $v->first('img'));

        @unlink($tmpFile);
    }
}
