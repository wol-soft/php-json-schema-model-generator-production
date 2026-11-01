<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\ComposedValue;

use Exception;
use PHPModelGenerator\Exception\ErrorRegistryExceptionInterface;
use PHPModelGenerator\Exception\MessageFormatter;
use PHPModelGenerator\Exception\ValidationException;

class ConditionalException extends ValidationException
{
    /**
     * @param $providedValue
     */
    public function __construct(
        $providedValue,
        string $propertyName,
        string $jsonPointer,
        private readonly ?Exception $ifException,
        private readonly ?Exception $thenException,
        private readonly ?Exception $elseException
    ) {
        parent::__construct($this->getErrorMessage($propertyName), $propertyName, $providedValue, $jsonPointer);

        // A conditional branch validates the same value at the same position as the conditional
        // itself: branch errors raised for the conditional property consume no path segment of
        // their own and inherit wherever this conditional ends up, see linkBranchExceptions().
        foreach ([$this->ifException, $this->thenException, $this->elseException] as $branchException) {
            $this->linkBranchExceptions($branchException);
        }
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
        $message = "Invalid value for '$propertyName' declined by conditional composition constraint\n";

        $message .= $this->ifException
            ? "  - Condition: Failed" . $this->getExceptionMessage($this->ifException)
            : '  - Condition: Valid';

        return $message . "\n  - Conditional branch failed:" .
            $this->getExceptionMessage($this->thenException ?: $this->elseException);
    }

    /**
     * Collected errors and a single exception render the same bullets: bulletList() only joins the errors, the
     * first bullet is prepended for both.
     */
    private function getExceptionMessage(Exception $exception): string
    {
        return "\n    * " . ($exception instanceof ErrorRegistryExceptionInterface
            ? MessageFormatter::bulletList($exception->getErrors())
            : str_replace("\n", "\n    ", $exception->getMessage()));
    }
}
