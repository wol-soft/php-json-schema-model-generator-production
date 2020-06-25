<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception;

use Throwable;

/**
 * Class ValidationException
 *
 * @package PHPModelGeneratorException
 */
abstract class ValidationException extends JSONModelValidationException
{
    /** @var string */
    protected $propertyName;
    /** @var mixed */
    protected $providedValue;

    /**
     * ValidationException constructor.
     *
     * @param string $message
     * @param string $propertyName
     * @param mixed $providedValue
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        string $message,
        string $propertyName,
        $providedValue,
        $code = 0,
        Throwable $previous = null
    ) {
        $this->propertyName = $propertyName;
        $this->providedValue = $providedValue;

        parent::__construct($message, $code, $previous);
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
