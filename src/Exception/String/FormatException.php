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
    /** @var string */
    protected $expectedFormat;

    /**
     * FormatException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     * @param string $expectedFormat
     */
    public function __construct($providedValue, string $propertyName, string $expectedFormat)
    {
        $this->expectedFormat = $expectedFormat;

        parent::__construct(
            "Value for $propertyName must match the format $expectedFormat",
            $propertyName,
            $providedValue
        );
    }

    /**
     * @return string
     */
    public function getExpectedFormat(): string
    {
        return $this->expectedFormat;
    }
}
