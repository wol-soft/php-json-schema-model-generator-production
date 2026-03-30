<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Dependency;

use PHPModelGenerator\Exception\ValidationException;
use Throwable;

/**
 * Class InvalidSchemaDependencyException
 *
 * @package PHPModelGenerator\Exception\Dependency
 */
class InvalidSchemaDependencyException extends ValidationException
{
    /**
     * InvalidSchemaDependencyException constructor.
     *
     * @param           $providedValue
     */
    public function __construct($providedValue, string $propertyName, protected \Throwable $dependencyException)
    {
        parent::__construct(
            "Invalid schema which is dependant on $propertyName:\n  - " .
                preg_replace(
                    "/\n([^\s])/m",
                    "\n  - $1",
                    (string) preg_replace("/\n\s/m", "\n     ", $this->dependencyException->getMessage())
                ),
            $propertyName,
            $providedValue
        );
    }

    public function getDependencyException(): Throwable
    {
        return $this->dependencyException;
    }
}
