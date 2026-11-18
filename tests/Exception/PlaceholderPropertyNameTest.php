<?php

declare(strict_types=1);

namespace PHPModelGenerator\Tests\Exception;

use Closure;
use PHPModelGenerator\Exception\Arrays\InvalidItemException;
use PHPModelGenerator\Exception\ComposedValue\AllOfException;
use PHPModelGenerator\Exception\ComposedValue\AnyOfException;
use PHPModelGenerator\Exception\ComposedValue\ConditionalException;
use PHPModelGenerator\Exception\ComposedValue\NotException;
use PHPModelGenerator\Exception\ComposedValue\OneOfException;
use PHPModelGenerator\Exception\ErrorRegistryException;
use PHPModelGenerator\Exception\Generic\InvalidTypeException;
use PHPModelGenerator\Exception\Object\AdditionalPropertiesException;
use PHPModelGenerator\Exception\Object\MaxPropertiesException;
use PHPModelGenerator\Exception\Object\MinPropertiesException;
use PHPModelGenerator\Exception\Object\NestedObjectException;
use PHPModelGenerator\Exception\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Exceptions raised by the validators of a generated class itself receive the class name as
 * property name. It is a label for the message text only and must not show up in the instance
 * pointer, neither of the exception itself nor of the exceptions nested below it.
 */
class PlaceholderPropertyNameTest extends TestCase
{
    /**
     * @return array<string, array{Closure(): ValidationException}>
     */
    public static function objectLevelExceptionProvider(): array
    {
        return [
            'minProperties' => [
                static fn(): ValidationException => new MinPropertiesException([], 'Model_Obj', '/minProperties', 1, 0),
            ],
            'maxProperties' => [
                static fn(): ValidationException => new MaxPropertiesException(
                    ['a' => 1, 'b' => 2],
                    'Model_Obj',
                    '/maxProperties',
                    1,
                    2,
                ),
            ],
            'additionalProperties: false' => [
                static fn(): ValidationException => new AdditionalPropertiesException(
                    ['c' => 1],
                    'Model_Obj',
                    '/additionalProperties',
                    ['c'],
                ),
            ],
        ];
    }

    /**
     * @return array<string, array{Closure(ValidationException): ValidationException}>
     */
    public static function compositionProvider(): array
    {
        return [
            'allOf' => [
                static fn(ValidationException $nested): ValidationException => new AllOfException(
                    ['b' => 'x'],
                    'Model_Obj',
                    '/allOf',
                    0,
                    [self::branch($nested)],
                ),
            ],
            'anyOf' => [
                static fn(ValidationException $nested): ValidationException => new AnyOfException(
                    ['b' => 'x'],
                    'Model_Obj',
                    '/anyOf',
                    0,
                    [self::branch($nested)],
                ),
            ],
            'oneOf' => [
                static fn(ValidationException $nested): ValidationException => new OneOfException(
                    ['b' => 'x'],
                    'Model_Obj',
                    '/oneOf',
                    0,
                    [self::branch($nested)],
                ),
            ],
            'not' => [
                static fn(ValidationException $nested): ValidationException => new NotException(
                    ['b' => 'x'],
                    'Model_Obj',
                    '/not',
                    1,
                    [self::branch($nested)],
                ),
            ],
            'if/then/else' => [
                static fn(ValidationException $nested): ValidationException => new ConditionalException(
                    ['b' => 'x'],
                    'Model_Obj',
                    '/if',
                    null,
                    self::branch($nested),
                    null,
                ),
            ],
        ];
    }

    /**
     * @param Closure(): ValidationException $createException
     */
    #[DataProvider('objectLevelExceptionProvider')]
    public function testObjectLevelExceptionOfANestedObjectPointsToTheObjectProperty(Closure $createException): void
    {
        $exception = $createException()->withPlaceholderPropertyName();

        new NestedObjectException(['obj' => []], 'obj', '/properties/obj', $exception);

        $this->assertSame('/obj', $exception->getInstancePointer()->pointer);
    }

    /**
     * @param Closure(): ValidationException $createException
     */
    #[DataProvider('objectLevelExceptionProvider')]
    public function testObjectLevelExceptionOfTheRootObjectHasAnEmptyPointer(Closure $createException): void
    {
        $this->assertSame('', $createException()->withPlaceholderPropertyName()->getInstancePointer()->pointer);
    }

    /**
     * @param Closure(ValidationException): ValidationException $createComposition
     */
    #[DataProvider('compositionProvider')]
    public function testCompositionOfANestedObjectResolvesNestedErrorsBelowTheObjectProperty(
        Closure $createComposition,
    ): void {
        $nestedException = new InvalidTypeException('x', 'b', '/properties/b/type', 'int');

        $composition = $createComposition($nestedException)->withPlaceholderPropertyName();

        new NestedObjectException(['obj' => ['b' => 'x']], 'obj', '/properties/obj', $composition);

        $this->assertSame('/obj', $composition->getInstancePointer()->pointer);
        $this->assertSame('/obj/b', $nestedException->getInstancePointer()->pointer);
    }

    /**
     * @param Closure(ValidationException): ValidationException $createComposition
     */
    #[DataProvider('compositionProvider')]
    public function testCompositionOfTheRootObjectResolvesNestedErrorsBelowTheRoot(Closure $createComposition): void
    {
        $nestedException = new InvalidTypeException('x', 'b', '/properties/b/type', 'int');

        $composition = $createComposition($nestedException)->withPlaceholderPropertyName();

        $this->assertSame('', $composition->getInstancePointer()->pointer);
        $this->assertSame('/b', $nestedException->getInstancePointer()->pointer);
    }

    /**
     * The exception classes are also used by validators of a property, where the property name
     * is a real name: without the marker the name is kept.
     */
    public function testExceptionsWithoutTheMarkerKeepTheirPropertyName(): void
    {
        $composition = new AnyOfException('x', 'name', '/properties/name/anyOf', 0, [new ErrorRegistryException()]);
        $minProperties = new MinPropertiesException([], 'obj', '/properties/obj/minProperties', 1, 0);

        $this->assertSame('/name', $composition->getInstancePointer()->pointer);
        $this->assertSame('/obj', $minProperties->getInstancePointer()->pointer);
    }

    public function testMarkerReturnsTheSameInstance(): void
    {
        $exception = new MinPropertiesException([], 'Model_Obj', '/minProperties', 1, 0);

        $this->assertSame($exception, $exception->withPlaceholderPropertyName());
    }

    /**
     * The instance pointer is resolved lazily: a marker set after the exception was already
     * linked to its parent and to its nested exceptions is reflected by all of them.
     */
    public function testMarkerSetAfterTheExceptionWasLinkedIsReflectedByTheWholeChain(): void
    {
        $nestedException = new InvalidTypeException('x', 'b', '/properties/b/type', 'int');
        $composition = new OneOfException(['b' => 'x'], 'Model_Obj', '/oneOf', 0, [self::branch($nestedException)]);

        new NestedObjectException(['obj' => ['b' => 'x']], 'obj', '/properties/obj', $composition);

        $this->assertSame('/obj/Model_Obj', $composition->getInstancePointer()->pointer);
        $this->assertSame('/obj/Model_Obj/b', $nestedException->getInstancePointer()->pointer);

        $composition->withPlaceholderPropertyName();

        $this->assertSame('/obj', $composition->getInstancePointer()->pointer);
        $this->assertSame('/obj/b', $nestedException->getInstancePointer()->pointer);
    }

    /**
     * An object-level exception which is an item of an array of objects is located by the array
     * index its wrapper assigns, not by the class name.
     */
    public function testSegmentAssignedByAWrapperAfterTheMarkerTakesPrecedence(): void
    {
        $nestedException = new InvalidTypeException('x', 'b', '/properties/b/type', 'int');
        $composition = (new AllOfException(['b' => 'x'], 'Model_Item', '/allOf', 0, [self::branch($nestedException)]))
            ->withPlaceholderPropertyName();

        new InvalidItemException([[], ['b' => 'x']], 'list', '/properties/list/items', [1 => [$composition]]);

        $this->assertSame('/list/1', $composition->getInstancePointer()->pointer);
        $this->assertSame('/list/1/b', $nestedException->getInstancePointer()->pointer);
    }

    private static function branch(ValidationException $error): ErrorRegistryException
    {
        $branch = new ErrorRegistryException();
        $branch->addError($error);

        return $branch;
    }
}
