<?php

declare(strict_types=1);

use App\Core\Http\Request;
use PHPUnit\Framework\TestCase;

class RequestAccessorTest extends TestCase
{
    private Request $request;

    protected function setUp(): void
    {
        // Simulate $_SERVER and $_POST to construct Request
        $_SERVER['REQUEST_URI'] = '/test';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'name' => 'John',
            'age' => '25',
            'score' => '9.5',
            'active' => '1',
            'tags' => ['php', 'mvc'],
            'empty_string' => '',
            'zero' => '0',
        ];

        $this->request = new Request();

        // Clean up superglobals
        $_POST = [];
    }

    // ------------------------------------------------------------------
    //  string()
    // ------------------------------------------------------------------

    public function testStringReturnsString(): void
    {
        $result = $this->request->string('name');
        $this->assertIsString($result);
        $this->assertSame('John', $result);
    }

    public function testStringReturnsDefaultWhenMissing(): void
    {
        $this->assertSame('default', $this->request->string('missing', 'default'));
    }

    public function testStringCastsNumericToString(): void
    {
        $this->assertSame('25', $this->request->string('age'));
    }

    // ------------------------------------------------------------------
    //  int()
    // ------------------------------------------------------------------

    public function testIntReturnsInteger(): void
    {
        $result = $this->request->int('age');
        $this->assertIsInt($result);
        $this->assertSame(25, $result);
    }

    public function testIntReturnsDefaultWhenMissing(): void
    {
        $this->assertSame(0, $this->request->int('missing'));
        $this->assertSame(99, $this->request->int('missing', 99));
    }

    public function testIntCastsStringToZero(): void
    {
        $this->assertSame(0, $this->request->int('name'));
    }

    // ------------------------------------------------------------------
    //  bool()
    // ------------------------------------------------------------------

    public function testBoolReturnsBool(): void
    {
        $result = $this->request->bool('active');
        $this->assertIsBool($result);
        $this->assertTrue($result);
    }

    public function testBoolReturnsFalseForZero(): void
    {
        $this->assertFalse($this->request->bool('zero'));
    }

    public function testBoolReturnsFalseForEmptyString(): void
    {
        $this->assertFalse($this->request->bool('empty_string'));
    }

    public function testBoolReturnsDefaultWhenMissing(): void
    {
        $this->assertFalse($this->request->bool('missing'));
        $this->assertTrue($this->request->bool('missing', true));
    }

    // ------------------------------------------------------------------
    //  float()
    // ------------------------------------------------------------------

    public function testFloatReturnsFloat(): void
    {
        $result = $this->request->float('score');
        $this->assertIsFloat($result);
        $this->assertSame(9.5, $result);
    }

    public function testFloatCastsIntegerString(): void
    {
        $this->assertSame(25.0, $this->request->float('age'));
    }

    public function testFloatReturnsDefaultWhenMissing(): void
    {
        $this->assertSame(0.0, $this->request->float('missing'));
        $this->assertSame(3.14, $this->request->float('missing', 3.14));
    }

    // ------------------------------------------------------------------
    //  array()
    // ------------------------------------------------------------------

    public function testArrayReturnsArray(): void
    {
        $result = $this->request->array('tags');
        $this->assertIsArray($result);
        $this->assertSame(['php', 'mvc'], $result);
    }

    public function testArrayWrapsScalarInArray(): void
    {
        $result = $this->request->array('name');
        $this->assertIsArray($result);
        $this->assertSame(['John'], $result);
    }

    public function testArrayReturnsDefaultWhenMissing(): void
    {
        $this->assertSame([], $this->request->array('missing'));
        $this->assertSame(['fallback'], $this->request->array('missing', ['fallback']));
    }

    // ------------------------------------------------------------------
    //  get() — generic accessor
    // ------------------------------------------------------------------

    public function testGetReturnsRawValue(): void
    {
        $this->assertSame('John', $this->request->get('name'));
    }

    public function testGetReturnsDefaultWhenMissing(): void
    {
        $this->assertNull($this->request->get('missing'));
        $this->assertSame('fallback', $this->request->get('missing', 'fallback'));
    }

    // ------------------------------------------------------------------
    //  has()
    // ------------------------------------------------------------------

    public function testHasReturnsTrueForExistingKey(): void
    {
        $this->assertTrue($this->request->has('name'));
    }

    public function testHasReturnsFalseForMissingKey(): void
    {
        $this->assertFalse($this->request->has('nonexistent'));
    }

    public function testHasReturnsTrueForEmptyString(): void
    {
        $this->assertTrue($this->request->has('empty_string'));
    }
}
