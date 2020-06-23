<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Generic;

use Exception;
use PHPModelGenerator\Exception\ValidationException;

/**
 * Class InvalidFilterValueException
 *
 * @package PHPModelGenerator\Exception\Generic
 */
class InvalidFilterValueException extends ValidationException
{
    /** @var string */
    protected $filterToken;
    /** @var Exception */
    protected $filterException;

    /**
     * InvalidFilterValueException constructor.
     *
     * @param           $providedValue
     * @param string    $propertyName
     * @param string    $filterToken
     * @param Exception $filterException
     */
    public function __construct($providedValue, string $propertyName, string $filterToken, Exception $filterException)
    {
        $this->filterToken = $filterToken;
        $this->filterException = $filterException;

        parent::__construct(
            sprintf(
                'Invalid value for property %s denied by filter %s: %s',
                $propertyName,
                $filterToken,
                $filterException->getMessage()
            ),
            $propertyName,
            $providedValue
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
     * @return Exception
     */
    public function getFilterException(): Exception
    {
        return $this->filterException;
    }
}
