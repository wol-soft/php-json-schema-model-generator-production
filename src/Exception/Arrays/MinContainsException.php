<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Arrays;

use PHPModelGenerator\Exception\ValidationException;

class MinContainsException extends ValidationException
{
    public function __construct(
        array $providedValue,
        string $propertyName,
        protected int $minContains,
        protected int $matches,
    ) {
        parent::__construct(
            sprintf(
                'Array %s must not contain less than %d items matching the contains constraint, %s matching items provided',
                $propertyName,
                $this->minContains,
                $this->matches,
            ),
            $propertyName,
            $providedValue
        );
    }

    public function getMinContains(): int
    {
        return $this->minContains;
    }

    public function getMatches(): int
    {
        return $this->matches;
    }
}
