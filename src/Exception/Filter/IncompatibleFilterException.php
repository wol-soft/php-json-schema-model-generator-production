<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Filter;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class IncompatibleFilterException
 *
 * @package PHPModelGenerator\Exception\Filter
 */
class IncompatibleFilterException extends ValidationException
{
    /**
     * IncompatibleFilterException constructor.
     *
     * @param $providedValue
     */
    public function __construct(
        $providedValue,
        string $propertyName,
        string $jsonPointer,
        protected string $filterToken
    ) {
        parent::__construct(
            sprintf(
                "Filter '%s' is not compatible with property type '%s' for property '%s'",
                $this->filterToken,
                gettype($providedValue),
                $propertyName
            ),
            $propertyName,
            $providedValue,
            $jsonPointer
        );
    }

    public function getFilterToken(): string
    {
        return $this->filterToken;
    }
}
