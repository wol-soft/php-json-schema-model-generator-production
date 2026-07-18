<?php

declare(strict_types=1);

namespace PHPModelGenerator\Tests\Exception;

use PHPModelGenerator\Exception\Arrays\InvalidItemException;
use PHPModelGenerator\Exception\Arrays\InvalidTupleException;
use PHPModelGenerator\Exception\ComposedValue\ConditionalException;
use PHPModelGenerator\Exception\ComposedValue\OneOfException;
use PHPModelGenerator\Exception\ErrorRegistryException;
use PHPModelGenerator\Exception\Generic\InvalidConstException;
use PHPModelGenerator\Exception\Generic\InvalidTypeException;
use PHPModelGenerator\Exception\Object\InvalidAdditionalPropertiesException;
use PHPModelGenerator\Exception\Object\InvalidPatternPropertiesException;
use PHPModelGenerator\Exception\Object\NestedObjectException;
use PHPModelGenerator\Exception\Object\RequiredValueException;
use PHPUnit\Framework\TestCase;

class InstancePointerTest extends TestCase
{
    public function testUnwrappedExceptionPointsToItsOwnPropertyName(): void
    {
        $exception = new RequiredValueException(null, 'name', '/properties/name/required');

        $this->assertSame('/name', $exception->getInstancePointer()->pointer);
    }

    public function testArrayItemExceptionIsPrefixedWithArrayNameAndIndex(): void
    {
        $itemException = new InvalidTypeException('1', 'tags', '/properties/tags/items/type', 'integer');

        new InvalidItemException(['a', '1'], 'tags', '/properties/tags/items', [1 => [$itemException]]);

        $this->assertSame('/tags/1', $itemException->getInstancePointer()->pointer);
    }

    public function testTupleItemExceptionIsPrefixedWithArrayNameAndIndex(): void
    {
        $tupleException = new InvalidTypeException(1, 'point', '/properties/point/items/0/type', 'string');

        new InvalidTupleException(['x' => 1], 'point', '/properties/point/items', [0 => [$tupleException]]);

        $this->assertSame('/point/0', $tupleException->getInstancePointer()->pointer);
    }

    public function testNestedArrayOfArraysComposesBothIndicesInOrder(): void
    {
        $leafException = new InvalidTypeException('ok', 'matrix', '/properties/matrix/items/items/type', 'integer');

        $innerItemException = new InvalidItemException(
            [1, 'ok'],
            'matrix',
            '/properties/matrix/items',
            [1 => [$leafException]],
        );

        new InvalidItemException(
            [[1, 2], [1, 'ok']],
            'matrix',
            '/properties/matrix/items',
            [1 => [$innerItemException]],
        );

        $this->assertSame('/matrix/1', $innerItemException->getInstancePointer()->pointer);
        $this->assertSame('/matrix/1/1', $leafException->getInstancePointer()->pointer);
    }

    /**
     * Regression test for the exact scenario from issue #131: a composition applied directly to
     * an array item must not introduce a spurious extra path segment from its own (borrowed)
     * propertyName — the composition is transparent, contributing no segment of its own; its
     * branch errors' pointers are resolved through the array item's position once the composition
     * exception itself is linked to the array wrapper.
     */
    public function testCompositionNestedInsideArrayItemResolvesToArrayIndexPlusBranchProperty(): void
    {
        $kindException = new InvalidConstException(
            'unknown',
            'kind',
            '/properties/goals/items/oneOf/0/properties/kind/const',
            'metric',
        );

        $branch = new ErrorRegistryException();
        $branch->addError($kindException);

        $compositionException = new OneOfException(
            ['kind' => 'unknown'],
            'goals',
            '/properties/goals/items/oneOf',
            0,
            [$branch],
        );

        new InvalidItemException(
            [['kind' => 'unknown']],
            'goals',
            '/properties/goals/items',
            [0 => [$compositionException]],
        );

        $this->assertSame('/goals/0', $compositionException->getInstancePointer()->pointer);
        $this->assertSame('/goals/0/kind', $kindException->getInstancePointer()->pointer);
    }

    /**
     * Same class of case as testCompositionNestedInsideArrayItemResolvesToArrayIndexPlusBranchProperty,
     * for a conditional (if/then/else) instead of oneOf/anyOf/allOf/not.
     */
    public function testConditionalNestedInsideArrayItemResolvesToArrayIndexPlusBranchProperty(): void
    {
        $branchException = new InvalidTypeException(
            1,
            'age',
            '/properties/goals/items/then/properties/age/type',
            'string',
        );

        $conditional = new ConditionalException(
            ['age' => 1],
            'goals',
            '/properties/goals/items/if',
            null,
            $branchException,
            null,
        );

        new InvalidItemException([['age' => 1]], 'goals', '/properties/goals/items', [0 => [$conditional]]);

        $this->assertSame('/goals/0', $conditional->getInstancePointer()->pointer);
        $this->assertSame('/goals/0/age', $branchException->getInstancePointer()->pointer);
    }

    public function testAdditionalPropertyExceptionIsPrefixedWithTheRealKeyNotTheClassNamePlaceholder(): void
    {
        $additionalPropertyException = new InvalidTypeException(
            42,
            'ClassPlaceholder',
            '/additionalProperties/type',
            'string',
        );

        $additionalPropertiesException = new InvalidAdditionalPropertiesException(
            ['b' => 42],
            'ClassPlaceholder',
            '/additionalProperties',
            ['b' => [$additionalPropertyException]],
        );

        new NestedObjectException(
            ['extra' => ['b' => 42]],
            'extra',
            '/properties/extra',
            $additionalPropertiesException,
        );

        $this->assertSame('/extra', $additionalPropertiesException->getInstancePointer()->pointer);
        $this->assertSame('/extra/b', $additionalPropertyException->getInstancePointer()->pointer);
    }

    public function testPatternPropertyExceptionIsPrefixedWithTheRealKeyNotTheClassNamePlaceholder(): void
    {
        $patternPropertyException = new InvalidTypeException(
            42,
            'ClassPlaceholder',
            '/patternProperties/^S~1/type',
            'string',
        );

        $patternPropertiesException = new InvalidPatternPropertiesException(
            ['S1' => 42],
            'ClassPlaceholder',
            '/patternProperties',
            '^S',
            ['S1' => [$patternPropertyException]],
        );

        new NestedObjectException(
            ['extra' => ['S1' => 42]],
            'extra',
            '/properties/extra',
            $patternPropertiesException,
        );

        $this->assertSame('/extra', $patternPropertiesException->getInstancePointer()->pointer);
        $this->assertSame('/extra/S1', $patternPropertyException->getInstancePointer()->pointer);
    }

    public function testInstancePointerSegmentsAreEscapedPerRfc6901(): void
    {
        $exception = new RequiredValueException(null, 'a/b~c', '/properties/a~1b~0c/required');

        $this->assertSame('/a~1b~0c', $exception->getInstancePointer()->pointer);
    }
}
