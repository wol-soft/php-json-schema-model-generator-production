<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Object;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class RegularPropertyAsAdditionalPropertyException
 *
 * @package PHPModelGenerator\Exception\Object
 */
class RegularPropertyAsAdditionalPropertyException extends ValidationException
{
    /**
     * @var string
     */
    private $class;

    /**
     * EnumException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     * @param string $class
     */
    public function __construct($providedValue, string $propertyName, string $class)
    {
        $this->class = $class;

        parent::__construct(
            sprintf("Couldn't add regular property %s as additional property to object %s", $propertyName, $class),
            $propertyName,
            $providedValue
        );
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }
}
