<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Object;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class MinLengthException
 *
 * @package PHPModelGenerator\Exception\Object
 */
class MinPropertiesException extends ValidationException
{
    /**
     * MinPropertiesException constructor.
     *
     * @param $providedValue
     */
    public function __construct($providedValue, string $propertyName, protected int $minProperties)
    {
        parent::__construct(
            "Provided object for $propertyName must not contain less than {$this->minProperties} properties",
            $propertyName,
            $providedValue
        );
    }

    public function getMinProperties(): int
    {
        return $this->minProperties;
    }
}
