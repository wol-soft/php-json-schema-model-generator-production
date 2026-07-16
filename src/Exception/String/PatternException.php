<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\String;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class PatternException
 *
 * @package PHPModelGenerator\Exception\String
 */
class PatternException extends ValidationException
{
    /**
     * PatternException constructor.
     *
     * @param $providedValue
     */
    public function __construct(
        $providedValue,
        string $propertyName,
        string $jsonPointer,
        protected string $expectedPattern
    ) {
        parent::__construct(
            "Value for '$propertyName' does not match pattern '{$this->expectedPattern}'",
            $propertyName,
            $providedValue,
            $jsonPointer
        );
    }

    public function getExpectedPattern(): string
    {
        return $this->expectedPattern;
    }
}
