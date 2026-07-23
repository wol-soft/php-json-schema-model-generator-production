<?php

declare(strict_types=1);

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
    public function __construct(
        $providedValue,
        string $propertyName,
        string $jsonPointer,
        protected string $expectedFormat
    ) {
        parent::__construct(
            "Value for '$propertyName' must match the format '{$this->expectedFormat}'",
            $propertyName,
            $providedValue,
            $jsonPointer
        );
    }

    public function getExpectedFormat(): string
    {
        return $this->expectedFormat;
    }
}
