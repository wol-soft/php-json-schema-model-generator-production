<?php

declare(strict_types = 1);

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
    /** @var string */
    protected $filterToken;

    /**
     * InvalidFilterValueException constructor.
     *
     * @param           $providedValue
     * @param string    $propertyName
     * @param string    $filterToken
     * @param Throwable $filterException
     */
    public function __construct($providedValue, string $propertyName, string $filterToken, Throwable $filterException)
    {
        $this->filterToken = $filterToken;

        parent::__construct(
            sprintf(
                'Invalid value for property %s denied by filter %s: %s',
                $propertyName,
                $filterToken,
                $filterException->getMessage()
            ),
            $propertyName,
            $providedValue,
            0,
            $filterException
        );
    }

    /**
     * @return string
     */
    public function getFilterToken(): string
    {
        return $this->filterToken;
    }

    /**
     * @return Throwable
     */
    public function getFilterException(): Throwable
    {
        return $this->getPrevious();
    }
}
