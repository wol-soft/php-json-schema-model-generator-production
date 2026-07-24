<?php

declare(strict_types=1);

namespace PHPModelGenerator\Tests\Exception\Generic;

use PHPModelGenerator\Exception\Generic\InvalidTypeException;
use PHPUnit\Framework\TestCase;
use stdClass;

class InvalidTypeExceptionTest extends TestCase
{
    public function testMessageReportsArrayForAListShapedValue(): void
    {
        $exception = new InvalidTypeException(['a', 'b'], 'tags', '/properties/tags/type', 'array');

        $this->assertSame("Invalid type for 'tags': requires 'array', got 'array'", $exception->getMessage());
    }

    public function testMessageReportsArrayForAnEmptyArray(): void
    {
        $exception = new InvalidTypeException([], 'tags', '/properties/tags/type', 'array');

        $this->assertSame("Invalid type for 'tags': requires 'array', got 'array'", $exception->getMessage());
    }

    public function testMessageReportsObjectForAMapShapedArray(): void
    {
        $exception = new InvalidTypeException(['a' => 'x', 'b' => 'y'], 'tags', '/properties/tags/type', 'array');

        $this->assertSame("Invalid type for 'tags': requires 'array', got 'object'", $exception->getMessage());
    }

    public function testMessageReportsObjectForAMapShapedArrayRegardlessOfTheExpectedType(): void
    {
        $exception = new InvalidTypeException(
            ['name' => 'Hans', 'age' => 42],
            'value',
            '/properties/value/type',
            'int',
        );

        $this->assertSame("Invalid type for 'value': requires 'int', got 'object'", $exception->getMessage());
    }

    public function testMessageReportsTheClassNameForAnObject(): void
    {
        $exception = new InvalidTypeException(new stdClass(), 'tags', '/properties/tags/type', 'array');

        $this->assertSame("Invalid type for 'tags': requires 'array', got 'stdClass'", $exception->getMessage());
    }

    public function testMessageReportsTheGettypeWordForAScalar(): void
    {
        $exception = new InvalidTypeException(42, 'name', '/properties/name/type', 'string');

        $this->assertSame("Invalid type for 'name': requires 'string', got 'integer'", $exception->getMessage());
    }

    public function testMessageEmbedsMultipleExpectedTypesAsAQuotedList(): void
    {
        $exception = new InvalidTypeException(
            ['a' => 1],
            'value',
            '/properties/value/type',
            ['string', 'int'],
        );

        $this->assertSame(
            "Invalid type for 'value': requires ['string', 'int'], got 'object'",
            $exception->getMessage(),
        );
        $this->assertSame(['string', 'int'], $exception->getExpectedType());
    }
}
