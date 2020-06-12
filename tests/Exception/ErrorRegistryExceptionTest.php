<?php

namespace PHPModelGenerator\Tests\Exception;

use PHPModelGenerator\Exception\ErrorRegistryException;
use PHPUnit\Framework\TestCase;

/**
 * Class ErrorRegistryExceptionTest
 *
 * @package PHPModelGeneratorException\Tests
 */
class ErrorRegistryExceptionTest extends TestCase
{
    /**
     * @throws ErrorRegistryException
     */
    public function testErrorRegistryExceptionIsThrowable(): void
    {
        $this->expectException(ErrorRegistryException::class);

        throw new ErrorRegistryException();
    }

    /**
     * @throws ErrorRegistryException
     */
    public function testErrorRegistryExceptionCollectsMessages(): void
    {
        $messages = [
            'Error Message 1',
            'Error Message 2',
        ];

        $this->expectException(ErrorRegistryException::class);
        $this->expectExceptionMessage(join("\n", $messages));

        $errorRegistry = new ErrorRegistryException();

        foreach ($messages as $message) {
            $errorRegistry->addError($message);
        }

        $this->assertSame($messages, $errorRegistry->getErrors());

        throw $errorRegistry;
    }
}
