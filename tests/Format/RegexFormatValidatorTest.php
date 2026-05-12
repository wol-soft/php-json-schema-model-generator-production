<?php

declare(strict_types=1);

namespace PHPModelGenerator\Tests\Format;

use PHPModelGenerator\Format\RegexFormatValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RegexFormatValidatorTest extends TestCase
{
    #[DataProvider('validProvider')]
    public function testValidRegex(string $input): void
    {
        $this->assertTrue(RegexFormatValidator::validate($input));
    }

    public static function validProvider(): array
    {
        return [
            'simple pattern'      => ['/^\d+$/'],
            'anchored'            => ['/^[a-z]+$/i'],
            'non-slash delimiter' => ['~^test~'],
            'empty pattern'       => ['//'],
        ];
    }

    #[DataProvider('invalidProvider')]
    public function testInvalidRegex(string $input): void
    {
        $this->assertFalse(RegexFormatValidator::validate($input));
    }

    public static function invalidProvider(): array
    {
        return [
            'unclosed bracket'   => ['/ab[c/'],
            'unclosed group'     => ['/ab(c/'],
        ];
    }
}
