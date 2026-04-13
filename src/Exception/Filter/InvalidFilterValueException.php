<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Filter;

use PHPModelGenerator\Exception\ValidationException;
use Throwable;

/**
 * Class InvalidFilterValueException
 *
 * @package PHPModelGenerator\Exception\Filter
 */
class InvalidFilterValueException extends ValidationException
{
    /**
     * InvalidFilterValueException constructor.
     *
     * @param           $providedValue
     */
    public function __construct($providedValue, string $propertyName, protected string $filterToken, Throwable $filterException)
    {
        parent::__construct(
            sprintf(
                'Invalid value for property %s denied by filter %s: %s',
                $propertyName,
                $this->filterToken,
                $filterException->getMessage()
            ),
            $propertyName,
            $providedValue,
            0,
            $filterException
        );
    }

    public function getFilterToken(): string
    {
        return $this->filterToken;
    }

    public function getFilterException(): Throwable
    {
        return $this->getPrevious();
    }
}
