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
    public function __construct($providedValue, string $propertyName, protected string $expectedPattern)
    {
        parent::__construct(
            "Value for $propertyName doesn't match pattern {$this->expectedPattern}",
            $propertyName,
            $providedValue
        );
    }

    public function getExpectedPattern(): string
    {
        return $this->expectedPattern;
    }
}
