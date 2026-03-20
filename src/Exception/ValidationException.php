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
    /**
     * ValidationException constructor.
     *
     * @param int $code
     */
    public function __construct(
        string $message,
        protected string $propertyName,
        protected mixed $providedValue,
        $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

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
