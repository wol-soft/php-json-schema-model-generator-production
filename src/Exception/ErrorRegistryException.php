<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception;

/**
 * Class ErrorRegistryException
 *
 * @package PHPModelGeneratorException
 */
class ErrorRegistryException extends JSONModelValidationException implements ErrorRegistryExceptionInterface
{
    protected $errors = [];

    /**
     * @inheritdoc
     */
    public function addError(ValidationException $exception): ErrorRegistryExceptionInterface
    {
        $this->errors[] = $exception;

        $this->message = join(
            "\n",
            array_map(
                function (ValidationException $e): string {
                    return $e->getMessage();
                },
                $this->errors
            )
        );

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
