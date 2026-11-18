<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Dependency;

use PHPModelGenerator\Exception\ErrorRegistryExceptionInterface;
use PHPModelGenerator\Exception\MessageFormatter;
use PHPModelGenerator\Exception\ValidationException;
use Throwable;

class InvalidSchemaDependencyException extends ValidationException
{
    /**
     * @param $providedValue
     */
    public function __construct(
        $providedValue,
        string $propertyName,
        string $jsonPointer,
        protected Throwable $dependencyException
    ) {
        if ($this->dependencyException instanceof ErrorRegistryExceptionInterface) {
            foreach ($this->dependencyException->getErrors() as $error) {
                $error->setInstancePointerParent($this);
            }
        } elseif ($this->dependencyException instanceof ValidationException) {
            $this->dependencyException->setInstancePointerParent($this);
        }

        parent::__construct(
            "Invalid schema which is dependant on '$propertyName':\n  - " .
                MessageFormatter::flattenNestedMessage($this->dependencyException->getMessage()),
            $propertyName,
            $providedValue,
            $jsonPointer
        );

        // This exception is located at the property which triggers the dependency, the same as
        // InvalidPropertyDependencyException. The dependency schema validates the object owning
        // that property, so its errors are located inside that object. Linking them below this
        // exception's own pointer as is would append the triggering property's name to them
        // (/obj/a/b instead of /obj/b); suppressing the segment altogether would move this
        // exception's own pointer from the triggering property to the object.
        $this->excludeOwnInstancePointerSegmentFromNestedErrors();
    }

    public function getDependencyException(): Throwable
    {
        return $this->dependencyException;
    }
}
