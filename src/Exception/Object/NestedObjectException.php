<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Object;

use Exception;
use PHPModelGenerator\Exception\ValidationException;

/**
 * Class NotAllowedAdditionalPropertiesException
 *
 * @package PHPModelGenerator\Exception\Object
 */
class NestedObjectException extends ValidationException
{
    /**
     * NotAllowedAdditionalPropertiesException constructor.
     *
     * @param $providedValue
     */
    public function __construct($providedValue, string $propertyName, private readonly Exception $nestedException)
    {
        parent::__construct(
            "Invalid nested object for property $propertyName:\n  - " .
                preg_replace(
                    "/\n([^\s])/m",
                    "\n  - $1",
                    (string) preg_replace("/\n\s/m", "\n     ", $this->nestedException->getMessage())
                ),
            $propertyName,
            $providedValue
        );
    }

    public function getNestedException(): Exception
    {
        return $this->nestedException;
    }
}
