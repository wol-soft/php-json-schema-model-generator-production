<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Generic;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class MissingRequiredValueException
 *
 * @package PHPModelGenerator\Exception\Generic
 */
class MissingRequiredValueException extends ValidationException
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
