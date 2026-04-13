<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Object;

use PHPModelGenerator\Exception\ValidationException;

class MinPropertiesException extends ValidationException
{
    public function __construct(
        $providedValue,
        string $propertyName,
        protected int $minProperties,
        protected int $count,
    ) {
        parent::__construct(
            sprintf(
                'Provided object for %s must not contain less than %d properties, %d properties provided',
                $propertyName,
                $this->minProperties,
                $this->count,
            ),
            $propertyName,
            $providedValue
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
