<?php

namespace PHPModelGenerator\Tests\Exception;

use PHPModelGenerator\Exception\ErrorRegistryException;
use PHPModelGenerator\Exception\Number\MaximumException;
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
            'test1' => 'Value for test1 must not be larger than 2',
            'test2' => 'Value for test2 must not be larger than 2',
        ];

        $this->expectException(ErrorRegistryException::class);
        $this->expectExceptionMessage(join("\n", $messages));

        $errorRegistry = new ErrorRegistryException();

        foreach ($messages as $property => $message) {
            $errorRegistry->addError(new MaximumException(10, $property, 2));
        }

        $this->assertCount(2, $errorRegistry->getErrors());

        $expectedOutput = [
            'errors'  =>
                [
                    0 =>
                        [
                            'maximum'       => 2,
                            'propertyName'  => 'test1',
                            'providedValue' => 10,
                            'message'       => 'Value for test1 must not be larger than 2',
                        ],
                    1 =>
                        [
                            'maximum'       => 2,
                            'propertyName'  => 'test2',
                            'providedValue' => 10,
                            'message'       => 'Value for test2 must not be larger than 2',
                        ],
                ],
            'message' => "Value for test1 must not be larger than 2\nValue for test2 must not be larger than 2",
        ];

        $this->assertSame($expectedOutput, $errorRegistry->toArray(['code']));
        $this->assertSame(json_encode($expectedOutput), $errorRegistry->toJSON(['code']));

        $serialized = $errorRegistry->toArray(['code'], 512, false);
        $this->assertArrayHasKey('file', $serialized);
        $this->assertArrayHasKey('line', $serialized);
        $this->assertArrayHasKey('file', $serialized['errors'][0]);
        $this->assertArrayHasKey('line', $serialized['errors'][0]);
        $this->assertArrayHasKey('file', json_decode($errorRegistry->toJSON(['code'], 0, 512, false), true));
        $this->assertArrayHasKey('line', json_decode($errorRegistry->toJSON(['code'], 0, 512, false), true));

        throw $errorRegistry;
    }
}
