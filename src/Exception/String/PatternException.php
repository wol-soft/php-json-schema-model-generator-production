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
    /** @var string */
    protected $expectedPattern;

    /**
     * PatternException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     * @param string $expectedPattern
     */
    public function __construct($providedValue, string $propertyName, string $expectedPattern)
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
