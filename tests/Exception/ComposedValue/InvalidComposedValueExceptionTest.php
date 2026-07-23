<?php

declare(strict_types=1);

namespace PHPModelGenerator\Tests\Exception\ComposedValue;

use PHPModelGenerator\Exception\ComposedValue\OneOfException;
use PHPModelGenerator\Exception\ErrorRegistryException;
use PHPModelGenerator\Exception\Generic\InvalidConstException;
use PHPUnit\Framework\TestCase;

class InvalidComposedValueExceptionTest extends TestCase
{
    /**
     * A composition element that itself fails a nested composition (in addition to a plain
     * constraint failure) must have the nested composition's multi-line message re-indented
     * under its own bullet, not spliced in at the outer composition's indentation level.
     *
     * Regression test for the flattened/merged output reported in issue #131: without
     * re-indentation, the inner "- Composition element #N" lines land at the same indentation
     * as the outer ones, making it look like a single, garbled list of sibling elements.
     */
    public function testNestedCompositionFailureIsReindentedUnderItsOwnBullet(): void
    {
        $kindConst1 = new InvalidConstException(
            'zzz',
            'kind',
            '/properties/target/oneOf/0/properties/kind/const',
            'x',
        );

        $reachUnitConstA = new InvalidConstException(
            'zzz',
            'reachUnit',
            '/properties/target/oneOf/0/properties/reachUnit/oneOf/0/const',
            'a',
        );
        $reachUnitConstB = new InvalidConstException(
            'zzz',
            'reachUnit',
            '/properties/target/oneOf/0/properties/reachUnit/oneOf/1/const',
            'b',
        );

        $reachUnitBranch1 = new ErrorRegistryException();
        $reachUnitBranch1->addError($reachUnitConstA);
        $reachUnitBranch2 = new ErrorRegistryException();
        $reachUnitBranch2->addError($reachUnitConstB);

        $reachUnitException = new OneOfException(
            'zzz',
            'reachUnit',
            '/properties/target/oneOf/0/properties/reachUnit/oneOf',
            0,
            [$reachUnitBranch1, $reachUnitBranch2],
        );

        $element1Errors = new ErrorRegistryException();
        $element1Errors->addError($kindConst1);
        $element1Errors->addError($reachUnitException);

        $kindConst2 = new InvalidConstException(
            'zzz',
            'kind',
            '/properties/target/oneOf/1/properties/kind/const',
            'y',
        );
        $element2Errors = new ErrorRegistryException();
        $element2Errors->addError($kindConst2);

        $targetException = new OneOfException(
            ['kind' => 'zzz', 'reachUnit' => 'zzz'],
            'target',
            '/properties/target/oneOf',
            0,
            [$element1Errors, $element2Errors],
        );

        $expectedMessage = <<<'MESSAGE'
        Invalid value for 'target' declined by composition constraint
          Requires to match one composition element but matched 0 elements
          - Composition element #1: Failed
            * Value for 'kind' must be "x", got "zzz"
            * Invalid value for 'reachUnit' declined by composition constraint
              Requires to match one composition element but matched 0 elements
              - Composition element #1: Failed
                * Value for 'reachUnit' must be "a", got "zzz"
              - Composition element #2: Failed
                * Value for 'reachUnit' must be "b", got "zzz"
          - Composition element #2: Failed
            * Value for 'kind' must be "y", got "zzz"
        MESSAGE;

        $this->assertSame($expectedMessage, $targetException->getMessage());
    }
}
