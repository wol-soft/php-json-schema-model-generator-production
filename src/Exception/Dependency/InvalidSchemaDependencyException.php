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
    /** @var Throwable */
    protected $dependencyException;

    /**
     * InvalidSchemaDependencyException constructor.
     *
     * @param           $providedValue
     * @param string    $propertyName
     * @param Throwable $dependencyException
     */
    public function __construct($providedValue, string $propertyName, Throwable $dependencyException)
    {
        $this->dependencyException = $dependencyException;

        parent::__construct(
            "Invalid schema which is dependant on $propertyName:\n  - " .
                preg_replace(
                    "/\n([^\s])/m",
                    "\n  - $1",
                    preg_replace("/\n\s/m", "\n     ", $dependencyException->getMessage())
                ),
            $propertyName,
            $providedValue
        );
    }

    /**
     * @return Throwable
     */
    public function getDependencyException(): Throwable
    {
        return $this->dependencyException;
    }
}
