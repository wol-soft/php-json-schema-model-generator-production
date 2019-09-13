<?php

declare(strict_types = 1);

namespace PHPModelGeneratorException;

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
     * @param string $message
     *
     * @return $this
     */
    public function addError(string $message): self;

    /**
     * Get all errors
     *
     * @return array
     */
    public function getErrors(): array;
}
