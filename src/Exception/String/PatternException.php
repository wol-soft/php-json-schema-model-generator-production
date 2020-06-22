<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\String;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class PatternException
 *
 * @package PHPModelGenerator\Exception\String
 */
class PatternException extends ValidationException
{
    protected $expectedPattern;

    public function __construct(string $propertyName, $providedValue, string $expectedPattern)
    {
        $this->expectedPattern = $expectedPattern;

        parent::__construct(
            "Value for $propertyName doesn't match pattern $expectedPattern",
            $propertyName,
            $providedValue
        );
    }

    /**
     * @return string
     */
    public function getExpectedPattern(): string
    {
        return $this->expectedPattern;
    }
}
