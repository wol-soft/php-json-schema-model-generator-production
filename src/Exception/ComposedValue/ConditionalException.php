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
    /** @var Exception|null */
    private $ifException;
    /** @var Exception|null */
    private $thenException;
    /** @var Exception|null */
    private $elseException;

    /**
     * ConditionalException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     * @param Exception|null $ifException
     * @param Exception|null $thenException
     * @param Exception|null $elseException
     */
    public function __construct(
        $providedValue,
        string $propertyName,
        ?Exception $ifException,
        ?Exception $thenException,
        ?Exception $elseException
    ) {

        // ,
        $this->ifException = $ifException;
        $this->thenException = $thenException;
        $this->elseException = $elseException;

        parent::__construct($this->getErrorMessage($propertyName), $propertyName, $providedValue);
    }

    /**
     * @return Exception|null
     */
    public function getIfException(): ?Exception
    {
        return $this->ifException;
    }

    /**
     * @return Exception|null
     */
    public function getThenException(): ?Exception
    {
        return $this->thenException;
    }

    /**
     * @return Exception|null
     */
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
                    array_map(function (ValidationException $exception): string {
                        return $exception->getMessage();
                    }, $exception->getErrors())
                )
            : "\n    * " . $exception->getMessage();
    }
}
