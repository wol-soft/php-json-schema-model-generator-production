<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception;

use Exception;

/**
 * Class ErrorRegistryException
 *
 * @package PHPModelGeneratorException
 */
class ErrorRegistryException extends Exception implements ErrorRegistryExceptionInterface
{
    protected $errors = [];

    /**
     * @inheritdoc
     */
    public function addError(string $message): ErrorRegistryExceptionInterface
    {
        $this->errors[] = $message;

        $this->message = join("\n", $this->errors);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
