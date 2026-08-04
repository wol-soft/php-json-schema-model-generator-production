<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Object;

use PHPModelGenerator\Exception\ValidationException;

class MinPropertiesException extends ValidationException
{
    public function __construct(
        $providedValue,
        string $propertyName,
        string $jsonPointer,
        protected int $minProperties,
        protected int $count,
    ) {
        parent::__construct(
            "Provided object for '$propertyName' must not contain less than {$this->minProperties} properties",
            $propertyName,
            $providedValue,
            $jsonPointer
        );
    }

    public function getMinProperties(): int
    {
        return $this->minProperties;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
