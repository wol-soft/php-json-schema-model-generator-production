<?php

namespace PHPModelGeneratorException\Tests;

use PHPModelGenerator\Exception\ValidationException;
use PHPUnit\Framework\TestCase;

/**
 * Class ValidationExceptionTest
 *
 * @package PHPModelGeneratorException\Tests
 */
class ValidationExceptionTest extends TestCase
{
    /**
     * @throws ValidationException
     */
    public function testValidationExceptionIsThrowable(): void
    {
        $this->expectException(ValidationException::class);

        throw new ValidationException();
    }
}
