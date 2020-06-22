<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception;

use Throwable;

/**
 * Interface ErrorRegistryExceptionInterface
 *
 * @package PHPModelGeneratorException
 */
interface ErrorRegistryExceptionInterface extends Throwable
{
    /**
     * Add an error to the error registry
     *
     * @param ValidationException $exception
     *
     * @return $this
     */
    public function addError(ValidationException $exception): self;

    /**
     * Get all errors
     *
     * @return ValidationException[]
     */
    public function getErrors(): array;
}
