<?php

declare(strict_types=1);

namespace PHPModelGenerator\Tests\Exception;

use PHPModelGenerator\Exception\Arrays\InvalidAdditionalTupleItemsException;
use PHPModelGenerator\Exception\Arrays\InvalidItemException;
use PHPModelGenerator\Exception\Arrays\InvalidTupleException;
use PHPModelGenerator\Exception\Arrays\InvalidUnevaluatedItemsException;
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

    /**
     * InvalidTupleException is keyed by the 1-based tuple position (the first element is "#1" in
     * the message) while the instance pointer addresses the 0-based array index.
     */
    public function testTupleItemExceptionIsPrefixedWithArrayNameAndZeroBasedIndex(): void
    {
        $tupleException = new InvalidTypeException(1, 'point', '/properties/point/items/0/type', 'string');

        new InvalidTupleException(['x' => 1], 'point', '/properties/point/items', [1 => [$tupleException]]);

        $this->assertSame('/point/0', $tupleException->getInstancePointer()->pointer);
    }

    public function testEveryTuplePositionMapsToThePrecedingZeroBasedIndex(): void
    {
        $firstException = new InvalidTypeException(1, 'point', '/properties/point/items/0/type', 'string');
        $secondException = new InvalidTypeException('b', 'point', '/properties/point/items/1/type', 'int');
        $thirdException = new InvalidTypeException(3, 'point', '/properties/point/items/2/type', 'string');

        new InvalidTupleException(
            [1, 'b', 3],
            'point',
            '/properties/point/items',
            [1 => [$firstException], 2 => [$secondException], 3 => [$thirdException]],
        );

        $this->assertSame('/point/0', $firstException->getInstancePointer()->pointer);
        $this->assertSame('/point/1', $secondException->getInstancePointer()->pointer);
        $this->assertSame('/point/2', $thirdException->getInstancePointer()->pointer);
    }

    /**
     * Value [["a", "x"], "b"] of a tuple "pair" whose first element is itself a tuple [string, int]:
     * the failing "x" is the second element of the first tuple, i.e. located at /pair/0/1.
     */
    public function testNestedTuplesComposeZeroBasedIndicesInOrder(): void
    {
        $leafException = new InvalidTypeException('x', 'pair', '/properties/pair/items/0/items/1/type', 'int');

        $innerTupleException = new InvalidTupleException(
            ['a', 'x'],
            'pair',
            '/properties/pair/items/0/items',
            [2 => [$leafException]],
        );

        new InvalidTupleException(
            [['a', 'x'], 'b'],
            'pair',
            '/properties/pair/items',
            [1 => [$innerTupleException]],
        );

        $this->assertSame('/pair/0', $innerTupleException->getInstancePointer()->pointer);
        $this->assertSame('/pair/0/1', $leafException->getInstancePointer()->pointer);
    }

    public function testTupleInsideArrayItemComposesItemIndexAndZeroBasedTupleIndex(): void
    {
        $leafException = new InvalidTypeException('x', 'matrix', '/properties/matrix/items/items/1/type', 'int');

        $tupleException = new InvalidTupleException(
            ['a', 'x'],
            'matrix',
            '/properties/matrix/items/items',
            [2 => [$leafException]],
        );

        new InvalidItemException(
            [['a', 1], ['a', 'x']],
            'matrix',
            '/properties/matrix/items',
            [1 => [$tupleException]],
        );

        $this->assertSame('/matrix/1', $tupleException->getInstancePointer()->pointer);
        $this->assertSame('/matrix/1/1', $leafException->getInstancePointer()->pointer);
    }

    public function testArrayInsideTupleComposesZeroBasedTupleIndexAndItemIndex(): void
    {
        $leafException = new InvalidTypeException('x', 'entry', '/properties/entry/items/1/items/type', 'int');

        $itemException = new InvalidItemException(
            [1, 'x'],
            'entry',
            '/properties/entry/items/1/items',
            [1 => [$leafException]],
        );

        new InvalidTupleException(
            ['a', [1, 'x']],
            'entry',
            '/properties/entry/items',
            [2 => [$itemException]],
        );

        $this->assertSame('/entry/1', $itemException->getInstancePointer()->pointer);
        $this->assertSame('/entry/1/1', $leafException->getInstancePointer()->pointer);
    }

    public function testUnevaluatedItemExceptionIsPrefixedWithArrayNameAndTheGivenIndex(): void
    {
        $itemException = new InvalidTypeException(2, 'list', '/properties/list/unevaluatedItems/type', 'string');

        new InvalidUnevaluatedItemsException(
            ['a', 'b', 2],
            'list',
            '/properties/list/unevaluatedItems',
            [2 => [$itemException]],
        );

        $this->assertSame('/list/2', $itemException->getInstancePointer()->pointer);
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

    /**
     * The root object has no name its own pointer could contribute: the class name handed to the
     * exception is a message label only, so neither the exception nor its nested errors may carry
     * it as a segment.
     */
    public function testAdditionalPropertiesOfTheRootObjectDoNotEmitTheClassNamePlaceholder(): void
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

        $this->assertSame('', $additionalPropertiesException->getInstancePointer()->pointer);
        $this->assertSame('/b', $additionalPropertyException->getInstancePointer()->pointer);
    }

    /**
     * Unlike the additional properties of an object, additional tuple items are validated by the
     * array property itself, so the exception receives the real array property name and has to
     * keep it as its own segment. The key is the true index within the whole array (the tuple
     * items are not subtracted).
     */
    public function testAdditionalTupleItemExceptionKeepsTheArrayNameAndUsesTheArrayIndex(): void
    {
        $additionalItemException = new InvalidTypeException('x', 'additional item', '/additionalItems/type', 'int');

        $additionalTupleItemsException = new InvalidAdditionalTupleItemsException(
            ['a', 1, 2, 'x'],
            'pair',
            '/properties/pair/additionalItems',
            [3 => [$additionalItemException]],
        );

        $this->assertSame('/pair', $additionalTupleItemsException->getInstancePointer()->pointer);
        $this->assertSame('/pair/3', $additionalItemException->getInstancePointer()->pointer);
    }

    public function testAdditionalTupleItemExceptionInsideArrayItemComposesItemIndexAndArrayIndex(): void
    {
        $additionalItemException = new InvalidTypeException('x', 'additional item', '/additionalItems/type', 'int');

        $additionalTupleItemsException = new InvalidAdditionalTupleItemsException(
            ['a', 1, 2, 'x'],
            'pair',
            '/properties/pair/items/additionalItems',
            [3 => [$additionalItemException]],
        );

        new InvalidItemException(
            [['a'], ['a', 1, 2, 'x']],
            'pair',
            '/properties/pair/items',
            [1 => [$additionalTupleItemsException]],
        );

        $this->assertSame('/pair/1', $additionalTupleItemsException->getInstancePointer()->pointer);
        $this->assertSame('/pair/1/3', $additionalItemException->getInstancePointer()->pointer);
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
