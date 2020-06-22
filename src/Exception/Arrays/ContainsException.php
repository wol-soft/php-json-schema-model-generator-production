<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Arrays;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class ContainsException
 *
 * @package PHPModelGenerator\Exception\Arrays
 */
class ContainsException extends ValidationException
{
    /**
     * ContainsException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     */
    public function __construct($providedValue, string $propertyName)
    {
        parent::__construct(
            "No item in array $propertyName matches contains constraint",
            $propertyName,
            $providedValue
        );
    }
}
