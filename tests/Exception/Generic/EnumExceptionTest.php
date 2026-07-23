<?php

declare(strict_types=1);

namespace PHPModelGenerator\Tests\Exception\Generic;

use PHPModelGenerator\Exception\Generic\EnumException;
use PHPUnit\Framework\TestCase;

class EnumExceptionTest extends TestCase
{
    public function testMessageEmbedsAllowedValuesAndProvidedValueAsJson(): void
    {
        $exception = new EnumException('zzz', 'label', '/properties/label/enum', ['a', 'b', 'c']);

        $this->assertSame('Value for \'label\' must be one of ["a","b","c"], got "zzz"', $exception->getMessage());
        $this->assertSame(['a', 'b', 'c'], $exception->getAllowedValues());
    }

    public function testMessageEmbedsNonScalarValuesAsCompactJson(): void
    {
        $exception = new EnumException(
            ['city' => 'LA'],
            'address',
            '/properties/address/enum',
            [['city' => 'NY'], ['city' => 'SF']],
        );

        $this->assertSame(
            'Value for \'address\' must be one of [{"city":"NY"},{"city":"SF"}], got {"city":"LA"}',
            $exception->getMessage(),
        );
    }

    public function testUpToEightAllowedValuesAreShownInFull(): void
    {
        $allowedValues = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];

        $exception = new EnumException('zzz', 'label', '/properties/label/enum', $allowedValues);

        $this->assertSame(
            'Value for \'label\' must be one of ["a","b","c","d","e","f","g","h"], got "zzz"',
            $exception->getMessage(),
        );
    }

    public function testMoreThanEightAllowedValuesAreTruncatedToFivePlusCount(): void
    {
        $allowedValues = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i'];

        $exception = new EnumException('zzz', 'label', '/properties/label/enum', $allowedValues);

        $this->assertSame(
            'Value for \'label\' must be one of ["a","b","c","d","e", ... (and 4 more)], got "zzz"',
            $exception->getMessage(),
        );
        $this->assertSame($allowedValues, $exception->getAllowedValues());
    }
}
