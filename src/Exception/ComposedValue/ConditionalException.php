<?php

declare(strict_types=1);

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
        string $jsonPointer,
        private readonly ?Exception $ifException,
        private readonly ?Exception $thenException,
        private readonly ?Exception $elseException
    ) {
        // A conditional branch validates the same value at the same position as the conditional
        // itself — it consumes no path segment of its own, so branch errors only need the parent
        // link (not a segment replacement) to inherit wherever this conditional ends up.
        foreach ([$this->ifException, $this->thenException, $this->elseException] as $branchException) {
            $this->linkInstancePointerParent($branchException);
        }

        parent::__construct($this->getErrorMessage($propertyName), $propertyName, $providedValue, $jsonPointer);
    }

    private function linkInstancePointerParent(?Exception $exception): void
    {
        if ($exception instanceof ErrorRegistryExceptionInterface) {
            foreach ($exception->getErrors() as $error) {
                $error->setInstancePointerParent($this);
            }
        } elseif ($exception instanceof ValidationException) {
            $exception->setInstancePointerParent($this);
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

    private function getExceptionMessage(Exception $exception): string
    {
        return $exception instanceof ErrorRegistryExceptionInterface
            ? implode(
                "\n    * ",
                str_replace(
                    "\n",
                    "\n    ",
                    array_map(
                        fn(ValidationException $exception): string => $exception->getMessage(),
                        $exception->getErrors(),
                    ),
                ),
            )
            : "\n    * " . str_replace("\n", "\n    ", $exception->getMessage());
    }
}
