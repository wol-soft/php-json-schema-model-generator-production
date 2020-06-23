<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Dependency;

use Exception;
use PHPModelGenerator\Exception\ValidationException;

/**
 * Class InvalidSchemaDependencyException
 *
 * @package PHPModelGenerator\Exception\Dependency
 */
class InvalidSchemaDependencyException extends ValidationException
{
    /** @var Exception */
    protected $dependencyException;

    /**
     * InvalidSchemaDependencyException constructor.
     *
     * @param           $providedValue
     * @param string    $propertyName
     * @param Exception $dependencyException
     */
    public function __construct($providedValue, string $propertyName, Exception $dependencyException)
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
     * @return Exception
     */
    public function getDependencyException(): Exception
    {
        return $this->dependencyException;
    }
}
