<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\String;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class FormatException
 *
 * @package PHPModelGenerator\Exception\String
 */
class FormatException extends ValidationException
{
    /**
     * FormatException constructor.
     *
     * @param $providedValue
     */
    public function __construct($providedValue, string $propertyName, protected string $expectedFormat)
    {
        parent::__construct(
            "Value for $propertyName must match the format {$this->expectedFormat}",
            $propertyName,
            $providedValue
        );
    }

    public function getExpectedFormat(): string
    {
        return $this->expectedFormat;
    }
}
