<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception;

/**
 * Class ValidationException
 *
 * @package PHPModelGeneratorException
 */
class ValidationException extends JSONModelValidationException
{
    protected $propertyName;
    protected $providedValue;

    /**
     * ValidationException constructor.
     *
     * @param string $message
     * @param string $propertyName
     * @param mixed  $providedValue
     */
    public function __construct(string $message, string $propertyName, $providedValue)
    {
        $this->propertyName = $propertyName;
        $this->providedValue = $providedValue;

        $this->message = $message;

        parent::__construct();
    }

    /**
     * @return string
     */
    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    /**
     * @return mixed
     */
    public function getProvidedValue()
    {
        return $this->providedValue;
    }
}
