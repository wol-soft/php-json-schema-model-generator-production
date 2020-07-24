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
    /** @var Exception */
    private $nestedException;

    /**
     * NotAllowedAdditionalPropertiesException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     * @param Exception $nestedException
     */
    public function __construct($providedValue, string $propertyName, Exception $nestedException)
    {
        $this->nestedException = $nestedException;

        parent::__construct(
            "Invalid nested object for property $propertyName:\n  - " .
                preg_replace(
                    "/\n([^\s])/m",
                    "\n  - $1",
                    preg_replace("/\n\s/m", "\n     ", $nestedException->getMessage())
                ),
            $propertyName,
            $providedValue
        );
    }

    /**
     * @return Exception
     */
    public function getNestedException(): Exception
    {
        return $this->nestedException;
    }
}
