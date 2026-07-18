<?php

declare(strict_types=1);

namespace PHPModelGenerator\Tests\Exception\Generic;

use PHPModelGenerator\Exception\Generic\InvalidConstException;
use PHPUnit\Framework\TestCase;

class InvalidConstExceptionTest extends TestCase
{
    public function testMessageEmbedsExpectedAndProvidedValueAsJson(): void
    {
        $exception = new InvalidConstException('nope', 'fixed', '/properties/fixed/const', 'hello');

        $this->assertSame('Value for \'fixed\' must be "hello", got "nope"', $exception->getMessage());
        $this->assertSame('hello', $exception->getExpectedValue());
    }

    public function testMessageEmbedsNonScalarValuesAsCompactJson(): void
    {
        $exception = new InvalidConstException(
            ['city' => 'LA'],
            'address',
            '/properties/address/const',
            ['city' => 'NY'],
        );

        $this->assertSame(
            'Value for \'address\' must be {"city":"NY"}, got {"city":"LA"}',
            $exception->getMessage(),
        );
    }

    public function testMessageEmbedsNullValues(): void
    {
        $exception = new InvalidConstException('nope', 'fixed', '/properties/fixed/const', null);

        $this->assertSame('Value for \'fixed\' must be null, got "nope"', $exception->getMessage());
    }
}
