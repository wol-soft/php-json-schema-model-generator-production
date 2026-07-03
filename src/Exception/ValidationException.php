<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception;

use PHPModelGenerator\Attributes\JsonPointer;
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
        protected string $jsonPointer,
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

    /**
     * The JSON pointer to the schema location of the constraint that rejected the value.
     */
    public function getJsonPointer(): JsonPointer
    {
        return new JsonPointer($this->jsonPointer);
    }
}
