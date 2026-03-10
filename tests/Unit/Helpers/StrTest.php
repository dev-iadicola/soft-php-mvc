<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Core\Helpers\Str;

class StrTest extends TestCase
{
    // ------------------------------------------------------------------
    //  Case conversion
    // ------------------------------------------------------------------

    public function testLower(): void
    {
        $this->assertSame('hello world', Str::lower('Hello World'));
        $this->assertSame('città', Str::lower('CITTÀ'));
    }

    public function testUpper(): void
    {
        $this->assertSame('HELLO WORLD', Str::upper('Hello World'));
    }

    public function testTitle(): void
    {
        $this->assertSame('Hello World', Str::title('hello world'));
    }

    public function testCamel(): void
    {
        $this->assertSame('helloWorld', Str::camel('hello_world'));
        $this->assertSame('helloWorld', Str::camel('hello-world'));
        $this->assertSame('helloWorld', Str::camel('Hello World'));
    }

    public function testSnake(): void
    {
        $this->assertSame('hello_world', Str::snake('HelloWorld'));
        $this->assertSame('hello_world', Str::snake('helloWorld'));
        $this->assertSame('html_parser', Str::snake('HTMLParser'));
    }

    public function testKebab(): void
    {
        $this->assertSame('hello-world', Str::kebab('HelloWorld'));
        $this->assertSame('hello-world', Str::kebab('helloWorld'));
    }

    public function testStudly(): void
    {
        $this->assertSame('HelloWorld', Str::studly('hello_world'));
        $this->assertSame('HelloWorld', Str::studly('hello-world'));
        $this->assertSame('HelloWorld', Str::studly('hello world'));
    }

    public function testSlug(): void
    {
        $this->assertSame('hello-world', Str::slug('Hello World'));
        $this->assertSame('hello_world', Str::slug('Hello World', '_'));
        $this->assertSame('hello-world', Str::slug('Hello  --  World'));
    }

    // ------------------------------------------------------------------
    //  Inspection helpers
    // ------------------------------------------------------------------

    public function testContains(): void
    {
        $this->assertTrue(Str::contains('Hello World', 'World'));
        $this->assertFalse(Str::contains('Hello World', 'world'));
        $this->assertTrue(Str::contains('Hello World', ['foo', 'World']));
        $this->assertFalse(Str::contains('Hello World', ''));
    }

    public function testStartsWith(): void
    {
        $this->assertTrue(Str::startsWith('Hello World', 'Hello'));
        $this->assertFalse(Str::startsWith('Hello World', 'World'));
        $this->assertTrue(Str::startsWith('Hello World', ['foo', 'Hello']));
    }

    public function testEndsWith(): void
    {
        $this->assertTrue(Str::endsWith('Hello World', 'World'));
        $this->assertFalse(Str::endsWith('Hello World', 'Hello'));
        $this->assertTrue(Str::endsWith('Hello World', ['foo', 'World']));
    }

    public function testLength(): void
    {
        $this->assertSame(5, Str::length('Hello'));
        $this->assertSame(5, Str::length('Città'));
    }

    public function testWordCount(): void
    {
        $this->assertSame(2, Str::wordCount('Hello World'));
        $this->assertSame(1, Str::wordCount('Hello'));
    }

    public function testIsEmpty(): void
    {
        $this->assertTrue(Str::isEmpty(''));
        $this->assertTrue(Str::isEmpty('   '));
        $this->assertFalse(Str::isEmpty('a'));
    }

    public function testIsNotEmpty(): void
    {
        $this->assertFalse(Str::isNotEmpty(''));
        $this->assertTrue(Str::isNotEmpty('a'));
    }

    public function testIs(): void
    {
        $this->assertTrue(Str::is('Hello*', 'Hello World'));
        $this->assertTrue(Str::is('*World', 'Hello World'));
        $this->assertTrue(Str::is('Hello', 'Hello'));
        $this->assertFalse(Str::is('foo*', 'Hello World'));
    }

    // ------------------------------------------------------------------
    //  Truncation
    // ------------------------------------------------------------------

    public function testLimit(): void
    {
        $this->assertSame('Hello...', Str::limit('Hello World', 5));
        $this->assertSame('Short', Str::limit('Short', 10));
        $this->assertSame('Hello--', Str::limit('Hello World', 5, '--'));
    }

    public function testWords(): void
    {
        $this->assertSame('Hello World...', Str::words('Hello World Foo Bar', 2));
        $this->assertSame('Short', Str::words('Short', 5));
    }

    // ------------------------------------------------------------------
    //  Sub-string extraction
    // ------------------------------------------------------------------

    public function testAfter(): void
    {
        $this->assertSame('World', Str::after('Hello World', 'Hello '));
        $this->assertSame('Hello World', Str::after('Hello World', ''));
        $this->assertSame('Hello World', Str::after('Hello World', 'missing'));
    }

    public function testBefore(): void
    {
        $this->assertSame('Hello', Str::before('Hello World', ' World'));
        $this->assertSame('Hello World', Str::before('Hello World', ''));
        $this->assertSame('Hello World', Str::before('Hello World', 'missing'));
    }

    public function testBetween(): void
    {
        $this->assertSame('is', Str::between('this is a test', 'this ', ' a'));
        $this->assertSame('[value]', Str::between('key=[value]&other', '=', '&'));
    }

    // ------------------------------------------------------------------
    //  Replacement
    // ------------------------------------------------------------------

    public function testReplace(): void
    {
        $this->assertSame('Hello PHP', Str::replace('World', 'PHP', 'Hello World'));
    }

    public function testReplaceFirst(): void
    {
        $this->assertSame('one two one', Str::replaceFirst('one', 'one', 'one two one'));
        $this->assertSame('X two one', Str::replaceFirst('one', 'X', 'one two one'));
    }

    public function testReplaceLast(): void
    {
        $this->assertSame('one two X', Str::replaceLast('one', 'X', 'one two one'));
    }

    // ------------------------------------------------------------------
    //  Casing helpers
    // ------------------------------------------------------------------

    public function testUcfirst(): void
    {
        $this->assertSame('Hello', Str::ucfirst('hello'));
        $this->assertSame('Hello', Str::ucfirst('Hello'));
    }

    public function testLcfirst(): void
    {
        $this->assertSame('hello', Str::lcfirst('Hello'));
        $this->assertSame('hello', Str::lcfirst('hello'));
    }

    // ------------------------------------------------------------------
    //  Pluralisation / Singularisation
    // ------------------------------------------------------------------

    public function testPlural(): void
    {
        $this->assertSame('articles', Str::plural('article'));
        $this->assertSame('technologies', Str::plural('technology'));
        $this->assertSame('buses', Str::plural('bus'));
        $this->assertSame('foxes', Str::plural('fox'));
        $this->assertSame('children', Str::plural('child'));
        $this->assertSame('sheep', Str::plural('sheep'));
        $this->assertSame('loaves', Str::plural('loaf'));
        $this->assertSame('', Str::plural(''));
    }

    public function testSingular(): void
    {
        $this->assertSame('article', Str::singular('articles'));
        $this->assertSame('technology', Str::singular('technologies'));
        $this->assertSame('bus', Str::singular('buses'));
        $this->assertSame('child', Str::singular('children'));
        $this->assertSame('sheep', Str::singular('sheep'));
        $this->assertSame('loaf', Str::singular('loaves'));
        $this->assertSame('', Str::singular(''));
    }

    // ------------------------------------------------------------------
    //  Random
    // ------------------------------------------------------------------

    public function testRandom(): void
    {
        $random = Str::random(32);
        $this->assertSame(32, strlen($random));
        $this->assertMatchesRegularExpression('/^[a-f0-9]+$/', $random);

        $this->assertSame(16, strlen(Str::random()));
    }
}
