<?php

declare(strict_types=1);

namespace PHPModelGenerator\Tests\Exception\ComposedValue;

use PHPModelGenerator\Exception\ComposedValue\ConditionalException;
use PHPModelGenerator\Exception\ComposedValue\OneOfException;
use PHPModelGenerator\Exception\ErrorRegistryException;
use PHPModelGenerator\Exception\Generic\InvalidConstException;
use PHPUnit\Framework\TestCase;

class ConditionalExceptionTest extends TestCase
{
    /**
     * A failed "then"/"else" branch that itself fails a nested composition must have that
     * nested composition's multi-line message re-indented under the "Conditional branch
     * failed:" bullet, not spliced in at the outer message's indentation level.
     *
     * Regression test for the same class of flattened-output bug reported in issue #131 for
     * InvalidComposedValueException.
     */
    public function testNestedCompositionFailureIsReindentedUnderTheBranchBullet(): void
    {
        $kindConstA = new InvalidConstException('zzz', 'kind', '/if/properties/kind/const', 'a');
        $kindConstB = new InvalidConstException('zzz', 'kind', '/if/properties/kind/const/1', 'b');

        $branch1 = new ErrorRegistryException();
        $branch1->addError($kindConstA);
        $branch2 = new ErrorRegistryException();
        $branch2->addError($kindConstB);

        $thenNestedComposition = new OneOfException(
            'zzz',
            'kind',
            '/then/properties/kind/oneOf',
            0,
            [$branch1, $branch2],
        );

        $thenErrors = new ErrorRegistryException();
        $thenErrors->addError($thenNestedComposition);

        $conditional = new ConditionalException(
            ['kind' => 'zzz'],
            'root',
            '/if',
            null,
            $thenErrors,
            null,
        );

        $expectedMessage = <<<'MESSAGE'
        Invalid value for 'root' declined by conditional composition constraint
          - Condition: Valid
          - Conditional branch failed:
            * Invalid value for 'kind' declined by composition constraint
              Requires to match one composition element but matched 0 elements
              - Composition element #1: Failed
                * Value for 'kind' must be "a", got "zzz"
              - Composition element #2: Failed
                * Value for 'kind' must be "b", got "zzz"
        MESSAGE;

        $this->assertSame($expectedMessage, $conditional->getMessage());
    }

    /**
     * Collected errors of the condition and of the failed branch (error collection mode) are rendered as bullets, each
     * error on its own line - identical to a single exception (direct exception mode).
     */
    public function testCollectedErrorsAreRenderedAsBullets(): void
    {
        $conditionError = new InvalidConstException('x', 'kind', '/if/properties/kind/const', 'a');
        $branchErrorA = new InvalidConstException('x', 'kind', '/else/properties/kind/const', 'b');
        $branchErrorB = new InvalidConstException(1, 'amount', '/else/properties/amount/const', 2);

        $conditionErrors = new ErrorRegistryException();
        $conditionErrors->addError($conditionError);
        $branchErrors = new ErrorRegistryException();
        $branchErrors->addError($branchErrorA);
        $branchErrors->addError($branchErrorB);

        $collected = new ConditionalException(['kind' => 'x'], 'root', '/if', $conditionErrors, null, $branchErrors);

        $this->assertSame(
            <<<'MESSAGE'
            Invalid value for 'root' declined by conditional composition constraint
              - Condition: Failed
                * Value for 'kind' must be "a", got "x"
              - Conditional branch failed:
                * Value for 'kind' must be "b", got "x"
                * Value for 'amount' must be 2, got 1
            MESSAGE,
            $collected->getMessage(),
        );

        $collectedSingleBranchError = new ErrorRegistryException();
        $collectedSingleBranchError->addError($branchErrorA);

        $this->assertSame(
            (new ConditionalException(['kind' => 'x'], 'root', '/if', $conditionError, null, $branchErrorA))
                ->getMessage(),
            (new ConditionalException(
                ['kind' => 'x'],
                'root',
                '/if',
                $conditionErrors,
                null,
                $collectedSingleBranchError,
            ))->getMessage(),
        );
    }
}
