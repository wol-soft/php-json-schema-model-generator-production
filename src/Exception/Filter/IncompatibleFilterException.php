<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Filter;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class IncompatibleFilterException
 *
 * @package PHPModelGenerator\Exception\Filter
 */
class IncompatibleFilterException extends ValidationException
{
    /** @var string */
    protected $filterToken;

    /**
     * IncompatibleFilterException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     * @param string $filterToken
     */
    public function __construct($providedValue, string $propertyName, string $filterToken)
    {
        $this->filterToken = $filterToken;

        parent::__construct(
            sprintf(
                'Filter %s is not compatible with property type %s for property %s',
                $filterToken,
                gettype($providedValue),
                $propertyName
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
}
