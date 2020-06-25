<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\ComposedValue;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class ConditionalException
 *
 * @package PHPModelGenerator\Exception\ComposedValue
 */
abstract class ConditionalException extends ValidationException
{
    /**
     * ConditionalException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     */
    public function __construct($providedValue, string $propertyName) {
        parent::__construct(
            "Invalid value for $propertyName declined by conditional composition constraint",
            $propertyName,
            $providedValue
        );
    }
}
