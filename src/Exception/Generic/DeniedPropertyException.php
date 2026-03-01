<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Generic;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class DeniedPropertyException
 *
 * @package PHPModelGenerator\Exception\Generic
 */
class DeniedPropertyException extends ValidationException
{
    /**
     * DeniedPropertyException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     */
    public function __construct($providedValue, string $propertyName)
    {
        parent::__construct("Value for $propertyName is not allowed", $propertyName, $providedValue);
    }
}
