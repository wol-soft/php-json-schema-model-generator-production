<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Object;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class MinLengthException
 *
 * @package PHPModelGenerator\Exception\Object
 */
class MinPropertiesException extends ValidationException
{
    /** @var int */
    protected $minProperties;

    /**
     * MinPropertiesException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     * @param int $minProperties
     */
    public function __construct($providedValue, string $propertyName, int $minProperties)
    {
        $this->minProperties = $minProperties;

        parent::__construct(
            "Provided object for $propertyName must not contain less than $minProperties properties",
            $propertyName,
            $providedValue
        );
    }

    /**
     * @return int
     */
    public function getMinProperties(): int
    {
        return $this->minProperties;
    }
}
