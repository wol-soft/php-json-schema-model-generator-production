<?php

declare(strict_types=1);

namespace PHPModelGenerator\Tests\Exception\Arrays;

use PHPModelGenerator\Exception\Arrays\InvalidTupleException;
use PHPModelGenerator\Exception\Generic\InvalidTypeException;
use PHPUnit\Framework\TestCase;

class InvalidTupleExceptionTest extends TestCase
{
    /**
     * The tuple keys are 1-based positions: the first element of the array is reported as "#1"
     * and the keys are exposed unchanged. Only the instance pointer is 0-based.
     */
    public function testTuplesAreNumberedStartingAtOneInMessageAndKeys(): void
    {
        $firstException = new InvalidTypeException(1, 'point', '/properties/point/items/0/type', 'string');
        $thirdException = new InvalidTypeException('c', 'point', '/properties/point/items/2/type', 'int');

        $invalidTuples = [1 => [$firstException], 3 => [$thirdException]];

        $exception = new InvalidTupleException([1, 'b', 'c'], 'point', '/properties/point/items', $invalidTuples);

        $this->assertSame(
            <<<'MESSAGE'
            Invalid tuple item in array 'point':
              - invalid tuple #1
                * Invalid type for 'point': requires 'string', got 'integer'
              - invalid tuple #3
                * Invalid type for 'point': requires 'int', got 'string'
            MESSAGE,
            $exception->getMessage(),
        );
        $this->assertSame($invalidTuples, $exception->getInvalidTuples());
        $this->assertSame([1, 3], array_keys($exception->getInvalidTuples()));

        $this->assertSame('/point/0', $firstException->getInstancePointer()->pointer);
        $this->assertSame('/point/2', $thirdException->getInstancePointer()->pointer);
    }

    public function testNestedTupleMessageKeepsTheOneBasedNumberingOnEveryLevel(): void
    {
        $leafException = new InvalidTypeException('x', 'pair', '/properties/pair/items/0/items/1/type', 'int');

        $innerTupleException = new InvalidTupleException(
            ['a', 'x'],
            'pair',
            '/properties/pair/items/0/items',
            [2 => [$leafException]],
        );

        $exception = new InvalidTupleException(
            [['a', 'x'], 'b'],
            'pair',
            '/properties/pair/items',
            [1 => [$innerTupleException]],
        );

        $this->assertSame(
            <<<'MESSAGE'
            Invalid tuple item in array 'pair':
              - invalid tuple #1
                * Invalid tuple item in array 'pair':
                  - invalid tuple #2
                    * Invalid type for 'pair': requires 'int', got 'string'
            MESSAGE,
            $exception->getMessage(),
        );
        $this->assertSame('/pair/0/1', $leafException->getInstancePointer()->pointer);
    }
}
