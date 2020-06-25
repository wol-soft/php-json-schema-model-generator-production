<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Object;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class RequiredValueException
 *
 * @package PHPModelGenerator\Exception\Object
 */
class RequiredValueException extends ValidationException
{
    /**
     * MissingRequiredValueException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     */
    public function __construct($providedValue, string $propertyName)
    {
        parent::__construct("Missing required value for $propertyName", $propertyName, $providedValue);
    }
}
