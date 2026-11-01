<?php

declare(strict_types=1);

namespace PHPModelGenerator\Tests\Exception\ComposedValue;

use Closure;
use PHPModelGenerator\Exception\Arrays\InvalidItemException;
use PHPModelGenerator\Exception\Arrays\MinItemsException;
use PHPModelGenerator\Exception\ComposedValue\AllOfException;
use PHPModelGenerator\Exception\ComposedValue\AnyOfException;
use PHPModelGenerator\Exception\ComposedValue\ConditionalException;
use PHPModelGenerator\Exception\ComposedValue\OneOfException;
use PHPModelGenerator\Exception\ErrorRegistryException;
use PHPModelGenerator\Exception\Generic\InvalidTypeException;
use PHPModelGenerator\Exception\Object\NestedObjectException;
use PHPModelGenerator\Exception\Object\RequiredValueException;
use PHPModelGenerator\Exception\String\MinLengthException;
use PHPModelGenerator\Exception\String\PatternException;
use PHPModelGenerator\Exception\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * The validators of a composition branch validate the same value as the composition itself and
 * raise their exceptions with the name of the composed property. A branch exception is therefore
 * located at the composition's location and must not append its own (identical) name again:
 * /name, not /name/name. Branch exceptions of an object carry the real, distinct names of the
 * object's properties and keep them (/obj/b).
 */
class CompositionInstancePointerTest extends TestCase
{
    /**
     * @return array<string, array{Closure(string, ErrorRegistryException[]): ValidationException}>
     */
    public static function compositionProvider(): array
    {
        return [
            'allOf' => [
                static fn(string $propertyName, array $branches): ValidationException => new AllOfException(
                    'ab',
                    $propertyName,
                    '/properties/name/allOf',
                    0,
                    $branches,
                ),
            ],
            'anyOf' => [
                static fn(string $propertyName, array $branches): ValidationException => new AnyOfException(
                    'ab',
                    $propertyName,
                    '/properties/name/anyOf',
                    0,
                    $branches,
                ),
            ],
            'oneOf' => [
                static fn(string $propertyName, array $branches): ValidationException => new OneOfException(
                    'ab',
                    $propertyName,
                    '/properties/name/oneOf',
                    0,
                    $branches,
                ),
            ],
        ];
    }

    /**
     * Every flavour of an if/then/else exception: each branch is a single exception or an error
     * registry, the condition either holds or fails with errors of its own.
     *
     * @return array<string, array{Closure(ValidationException, ValidationException): ConditionalException}>
     */
    public static function conditionalProvider(): array
    {
        return [
            'condition and then branch as single exceptions' => [
                static fn(ValidationException $first, ValidationException $second): ConditionalException
                    => new ConditionalException('ab', 'name', '/properties/name/if', $first, $second, null),
            ],
            'condition and else branch as single exceptions' => [
                static fn(ValidationException $first, ValidationException $second): ConditionalException
                    => new ConditionalException('ab', 'name', '/properties/name/if', $first, null, $second),
            ],
            'condition and else branch as error registries' => [
                static fn(ValidationException $first, ValidationException $second): ConditionalException
                    => new ConditionalException(
                        'ab',
                        'name',
                        '/properties/name/if',
                        self::registry($first),
                        null,
                        self::registry($second),
                    ),
            ],
            'then branch with several collected errors and a valid condition' => [
                static fn(ValidationException $first, ValidationException $second): ConditionalException
                    => new ConditionalException(
                        'ab',
                        'name',
                        '/properties/name/if',
                        null,
                        self::registry($first, $second),
                        null,
                    ),
            ],
        ];
    }

    /**
     * @param Closure(string, ErrorRegistryException[]): ValidationException $createComposition
     */
    #[DataProvider('compositionProvider')]
    public function testScalarBranchErrorsAreLocatedAtTheComposedProperty(Closure $createComposition): void
    {
        $minLengthException = new MinLengthException('ab', 'name', '/properties/name/anyOf/0/minLength', 5);
        $patternException = new PatternException('ab', 'name', '/properties/name/anyOf/1/pattern', '^x');

        $composition = $createComposition(
            'name',
            [self::registry($minLengthException), self::registry($patternException)],
        );

        $this->assertSame('/name', $composition->getInstancePointer()->pointer);
        $this->assertSame('/name', $minLengthException->getInstancePointer()->pointer);
        $this->assertSame('/name', $patternException->getInstancePointer()->pointer);
    }

    /**
     * @param Closure(string, ErrorRegistryException[]): ValidationException $createComposition
     */
    #[DataProvider('compositionProvider')]
    public function testSeveralCollectedErrorsOfOneBranchAreLocatedAtTheComposedProperty(
        Closure $createComposition,
    ): void {
        $minLengthException = new MinLengthException('ab', 'name', '/properties/name/allOf/0/minLength', 5);
        $patternException = new PatternException('ab', 'name', '/properties/name/allOf/0/pattern', '^x');

        $composition = $createComposition('name', [self::registry($minLengthException, $patternException)]);

        $this->assertSame('/name', $composition->getInstancePointer()->pointer);
        $this->assertSame('/name', $minLengthException->getInstancePointer()->pointer);
        $this->assertSame('/name', $patternException->getInstancePointer()->pointer);
    }

    /**
     * @param Closure(ValidationException, ValidationException): ConditionalException $createConditional
     */
    #[DataProvider('conditionalProvider')]
    public function testConditionalBranchErrorsAreLocatedAtTheComposedProperty(Closure $createConditional): void
    {
        $firstException = new MinLengthException('ab', 'name', '/properties/name/if/minLength', 5);
        $secondException = new PatternException('ab', 'name', '/properties/name/then/pattern', '^x');

        $conditional = $createConditional($firstException, $secondException);

        $this->assertSame('/name', $conditional->getInstancePointer()->pointer);
        $this->assertSame('/name', $firstException->getInstancePointer()->pointer);
        $this->assertSame('/name', $secondException->getInstancePointer()->pointer);
    }

    /**
     * @param Closure(string, ErrorRegistryException[]): ValidationException $createComposition
     */
    #[DataProvider('compositionProvider')]
    public function testArrayBranchErrorsAreLocatedAtTheComposedArrayAndItsIndices(Closure $createComposition): void
    {
        $firstTypeException = new InvalidTypeException('a', 'tags', '/properties/tags/anyOf/0/items/type', 'int');
        $secondTypeException = new InvalidTypeException('b', 'tags', '/properties/tags/anyOf/0/items/type', 'int');
        $itemException = new InvalidItemException(
            ['a', 'b'],
            'tags',
            '/properties/tags/anyOf/0/items',
            [0 => [$firstTypeException], 1 => [$secondTypeException]],
        );
        $minItemsException = new MinItemsException(['a', 'b'], 'tags', '/properties/tags/anyOf/1/minItems', 5, 2);

        $composition = $createComposition('tags', [self::registry($itemException), self::registry($minItemsException)]);

        $this->assertSame('/tags', $composition->getInstancePointer()->pointer);
        $this->assertSame('/tags', $itemException->getInstancePointer()->pointer);
        $this->assertSame('/tags/0', $firstTypeException->getInstancePointer()->pointer);
        $this->assertSame('/tags/1', $secondTypeException->getInstancePointer()->pointer);
        $this->assertSame('/tags', $minItemsException->getInstancePointer()->pointer);
    }

    public function testCompositionNestedInACompositionOfTheSamePropertyIsLocatedAtThatProperty(): void
    {
        $minLengthException = new MinLengthException('ab', 'name', '/properties/name/anyOf/0/allOf/0/minLength', 5);
        $patternException = new PatternException('ab', 'name', '/properties/name/anyOf/1/pattern', '^x');

        $innerComposition = new AllOfException(
            'ab',
            'name',
            '/properties/name/anyOf/0/allOf',
            0,
            [self::registry($minLengthException)],
        );

        $outerComposition = new AnyOfException(
            'ab',
            'name',
            '/properties/name/anyOf',
            0,
            [self::registry($innerComposition), self::registry($patternException)],
        );

        $this->assertSame('/name', $outerComposition->getInstancePointer()->pointer);
        $this->assertSame('/name', $innerComposition->getInstancePointer()->pointer);
        $this->assertSame('/name', $minLengthException->getInstancePointer()->pointer);
        $this->assertSame('/name', $patternException->getInstancePointer()->pointer);
    }

    /**
     * A composition applied to an array item is located at the item by the array index its
     * wrapper assigns; the branch errors are located at the same item.
     */
    public function testBranchErrorsOfACompositionOfAnArrayItemFollowTheAssignedIndex(): void
    {
        $minLengthException = new MinLengthException('ab', 'tags', '/properties/tags/items/anyOf/0/minLength', 5);
        $patternException = new PatternException('ab', 'tags', '/properties/tags/items/anyOf/1/pattern', '^x');

        $composition = new AnyOfException(
            'ab',
            'tags',
            '/properties/tags/items/anyOf',
            0,
            [self::registry($minLengthException), self::registry($patternException)],
        );

        new InvalidItemException(['abcdef', 'ab'], 'tags', '/properties/tags/items', [1 => [$composition]]);

        $this->assertSame('/tags/1', $composition->getInstancePointer()->pointer);
        $this->assertSame('/tags/1', $minLengthException->getInstancePointer()->pointer);
        $this->assertSame('/tags/1', $patternException->getInstancePointer()->pointer);
    }

    /**
     * A property named like the composed property inside of a branch which validates an object is
     * a different value than the composition: the branch exception is the nested object exception
     * (located at the composed property), the exceptions of the object's properties keep their
     * own name.
     */
    public function testPropertyOfAnObjectBranchNamedLikeTheComposedPropertyKeepsItsName(): void
    {
        $missingException = new RequiredValueException(['b' => 1], 'obj', '/properties/obj/anyOf/0/required');

        $objectException = new NestedObjectException(
            ['b' => 1],
            'obj',
            '/properties/obj/anyOf/0',
            $missingException,
        );

        $composition = new AnyOfException(
            ['b' => 1],
            'obj',
            '/properties/obj/anyOf',
            0,
            [self::registry($objectException)],
        );

        $this->assertSame('/obj', $composition->getInstancePointer()->pointer);
        $this->assertSame('/obj', $objectException->getInstancePointer()->pointer);
        $this->assertSame('/obj/obj', $missingException->getInstancePointer()->pointer);
    }

    /**
     * Branch exceptions of a composition of an object carry the names of the object's properties
     * (the composition itself is named after the object) and are located below the composition.
     *
     * @param Closure(string, ErrorRegistryException[]): ValidationException $createComposition
     */
    #[DataProvider('compositionProvider')]
    public function testBranchErrorsWithADifferentNameKeepTheirName(Closure $createComposition): void
    {
        $nestedException = new InvalidTypeException('x', 'b', '/properties/obj/anyOf/0/properties/b/type', 'int');

        $composition = $createComposition('obj', [self::registry($nestedException)]);

        $this->assertSame('/obj', $composition->getInstancePointer()->pointer);
        $this->assertSame('/obj/b', $nestedException->getInstancePointer()->pointer);
    }

    public function testConditionalBranchErrorsWithADifferentNameKeepTheirName(): void
    {
        $thenException = new InvalidTypeException('x', 'b', '/properties/obj/then/properties/b/type', 'int');

        $conditional = new ConditionalException(['b' => 'x'], 'obj', '/properties/obj/if', null, $thenException, null);

        $this->assertSame('/obj', $conditional->getInstancePointer()->pointer);
        $this->assertSame('/obj/b', $thenException->getInstancePointer()->pointer);
    }

    private static function registry(ValidationException ...$errors): ErrorRegistryException
    {
        $registry = new ErrorRegistryException();
        foreach ($errors as $error) {
            $registry->addError($error);
        }

        return $registry;
    }
}
