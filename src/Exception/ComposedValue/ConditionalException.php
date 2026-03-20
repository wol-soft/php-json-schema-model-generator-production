<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\ComposedValue;

use Exception;
use PHPModelGenerator\Exception\ErrorRegistryExceptionInterface;
use PHPModelGenerator\Exception\ValidationException;

/**
 * Class ConditionalException
 *
 * @package PHPModelGenerator\Exception\ComposedValue
 */
class ConditionalException extends ValidationException
{
    /**
     * ConditionalException constructor.
     *
     * @param $providedValue
     */
    public function __construct(
        $providedValue,
        string $propertyName,
        private readonly ?Exception $ifException,
        private readonly ?Exception $thenException,
        private readonly ?Exception $elseException
    ) {

        parent::__construct($this->getErrorMessage($propertyName), $propertyName, $providedValue);
    }

    public function getIfException(): ?Exception
    {
        return $this->ifException;
    }

    public function getThenException(): ?Exception
    {
        return $this->thenException;
    }

    public function getElseException(): ?Exception
    {
        return $this->elseException;
    }

    private function getErrorMessage(string $propertyName): string
    {
        $message = "Invalid value for $propertyName declined by conditional composition constraint\n";

        $message .= $this->ifException
            ? "  - Condition: Failed" . $this->getExceptionMessage($this->ifException)
            :'  - Condition: Valid';

        return $message . "\n  - Conditional branch failed:" .
            $this->getExceptionMessage($this->thenException ?: $this->elseException);
    }

    private function getExceptionMessage(Exception $exception): string
    {
        return $exception instanceof ErrorRegistryExceptionInterface
            ? implode(
                    "\n    * ",
                    array_map(fn(ValidationException $exception): string => $exception->getMessage(), $exception->getErrors())
                )
            : "\n    * " . $exception->getMessage();
    }
}
