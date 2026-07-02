<?php

declare(strict_types=1);

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
     * EnumException constructor.
     *
     * @param $providedValue
     */
    public function __construct(
        $providedValue,
        string $propertyName,
        string $jsonPointer,
        private readonly string $class
    ) {
        parent::__construct(
            sprintf("Couldn't add regular property %s as additional property to object %s", $propertyName, $this->class),
            $propertyName,
            $providedValue,
            $jsonPointer
        );
    }

    public function getClass(): string
    {
        return $this->class;
    }
}
