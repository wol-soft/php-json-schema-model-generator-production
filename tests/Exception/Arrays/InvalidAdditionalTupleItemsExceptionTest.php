<?php

declare(strict_types=1);

namespace PHPModelGenerator\Tests\Exception\Arrays;

use PHPModelGenerator\Exception\Arrays\InvalidAdditionalTupleItemsException;
use PHPModelGenerator\Exception\Generic\InvalidTypeException;
use PHPUnit\Framework\TestCase;

class InvalidAdditionalTupleItemsExceptionTest extends TestCase
{
    public function testMessageNamesTheTupleArrayAndTheArrayIndexOfTheInvalidAdditionalItem(): void
    {
        $additionalItemException = new InvalidTypeException('x', 'additional item', '/additionalItems/type', 'int');

        $exception = new InvalidAdditionalTupleItemsException(
            ['a', 1, 2, 'x'],
            'pair',
            '/properties/pair/additionalItems',
            [3 => [$additionalItemException]],
        );

        $this->assertSame(
            <<<'MESSAGE'
            Tuple array 'pair' contains invalid additional items
              - invalid additional item '3'
                * Invalid type for 'additional item': requires 'int', got 'string'
            MESSAGE,
            $exception->getMessage(),
        );
        $this->assertSame([3 => [$additionalItemException]], $exception->getNestedExceptions());
        $this->assertSame('pair', $exception->getPropertyName());
    }
}
