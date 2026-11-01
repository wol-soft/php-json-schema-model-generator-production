<?php

declare(strict_types=1);

namespace PHPModelGenerator\Tests\Exception\Dependency;

use PHPModelGenerator\Exception\Arrays\InvalidItemException;
use PHPModelGenerator\Exception\Dependency\InvalidPropertyDependencyException;
use PHPModelGenerator\Exception\Dependency\InvalidSchemaDependencyException;
use PHPModelGenerator\Exception\ErrorRegistryException;
use PHPModelGenerator\Exception\Object\NestedObjectException;
use PHPModelGenerator\Exception\Object\RequiredValueException;
use PHPUnit\Framework\TestCase;

/**
 * A dependency exception is located at the property which triggers the dependency (/obj/a). The
 * exceptions of the dependency schema validate the object owning that property, so they are
 * located inside the object (/obj/b), not below the triggering property (/obj/a/b).
 */
class DependencyInstancePointerTest extends TestCase
{
    public function testSchemaDependencyKeepsItsPointerAndLocatesTheNestedErrorInsideTheObject(): void
    {
        $missingException = new RequiredValueException(['a' => 1], 'b', '/properties/b/required');

        $dependencyException = new InvalidSchemaDependencyException(
            ['a' => 1],
            'a',
            '/dependencies/a',
            $missingException,
        );

        new NestedObjectException(['obj' => ['a' => 1]], 'obj', '/properties/obj', $dependencyException);

        $this->assertSame('/obj/a', $dependencyException->getInstancePointer()->pointer);
        $this->assertSame('/obj/b', $missingException->getInstancePointer()->pointer);
    }

    public function testSchemaDependencyLocatesEveryCollectedErrorInsideTheObject(): void
    {
        $missingExceptionB = new RequiredValueException(['a' => 1], 'b', '/properties/b/required');
        $missingExceptionC = new RequiredValueException(['a' => 1], 'c', '/properties/c/required');

        $errors = new ErrorRegistryException();
        $errors->addError($missingExceptionB);
        $errors->addError($missingExceptionC);

        $dependencyException = new InvalidSchemaDependencyException(['a' => 1], 'a', '/dependencies/a', $errors);

        new NestedObjectException(['obj' => ['a' => 1]], 'obj', '/properties/obj', $dependencyException);

        $this->assertSame('/obj/a', $dependencyException->getInstancePointer()->pointer);
        $this->assertSame('/obj/b', $missingExceptionB->getInstancePointer()->pointer);
        $this->assertSame('/obj/c', $missingExceptionC->getInstancePointer()->pointer);
    }

    public function testSchemaDependencyOfTheRootObject(): void
    {
        $missingException = new RequiredValueException(['a' => 1], 'b', '/properties/b/required');

        $dependencyException = new InvalidSchemaDependencyException(
            ['a' => 1],
            'a',
            '/dependencies/a',
            $missingException,
        );

        $this->assertSame('/a', $dependencyException->getInstancePointer()->pointer);
        $this->assertSame('/b', $missingException->getInstancePointer()->pointer);
    }

    /**
     * Same pointer for both draft-07 dependency kinds: the property which triggers the dependency.
     */
    public function testPropertyDependencyIsLocatedAtTheTriggeringProperty(): void
    {
        $dependencyException = new InvalidPropertyDependencyException(
            ['a' => 1],
            'a',
            '/dependencies/a',
            ['b'],
        );

        new NestedObjectException(['obj' => ['a' => 1]], 'obj', '/properties/obj', $dependencyException);

        $this->assertSame('/obj/a', $dependencyException->getInstancePointer()->pointer);
    }

    /**
     * When a wrapper assigns the position of the validated value (an array index), that position
     * is the location of the object, so the nested errors are located below it.
     */
    public function testSegmentAssignedByAWrapperIsKeptForTheNestedErrors(): void
    {
        $missingException = new RequiredValueException(['a' => 1], 'b', '/properties/b/required');

        $dependencyException = new InvalidSchemaDependencyException(
            ['a' => 1],
            'a',
            '/dependencies/a',
            $missingException,
        );

        new InvalidItemException([[], ['a' => 1]], 'list', '/properties/list/items', [1 => [$dependencyException]]);

        $this->assertSame('/list/1', $dependencyException->getInstancePointer()->pointer);
        $this->assertSame('/list/1/b', $missingException->getInstancePointer()->pointer);
    }

    public function testSchemaDependencyMessage(): void
    {
        $missingException = new RequiredValueException(['a' => 1], 'b', '/properties/b/required');

        $dependencyException = new InvalidSchemaDependencyException(
            ['a' => 1],
            'a',
            '/dependencies/a',
            $missingException,
        );

        $this->assertSame(
            <<<'MESSAGE'
            Invalid schema which is dependant on 'a':
              - Missing required value for 'b'
            MESSAGE,
            $dependencyException->getMessage(),
        );
        $this->assertSame($missingException, $dependencyException->getDependencyException());
    }
}
