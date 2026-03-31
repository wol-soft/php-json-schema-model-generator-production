<?php

declare(strict_types=1);

namespace PHPModelGenerator\Tests\Format;

use PHPModelGenerator\Format\IriFormatValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class IriFormatValidatorTest extends TestCase
{
    #[DataProvider('validProvider')]
    public function testValidIri(string $input): void
    {
        $this->assertTrue(IriFormatValidator::validate($input));
    }

    public static function validProvider(): array
    {
        return [
            'ascii uri'       => ['http://example.com/path'],
            'unicode path'    => ['http://example.com/München'],
            'unicode host'    => ['http://müller.example.com/'],
        ];
    }

    #[DataProvider('invalidProvider')]
    public function testInvalidIri(string $input): void
    {
        $this->assertFalse(IriFormatValidator::validate($input));
    }

    public static function invalidProvider(): array
    {
        return [
            'no scheme'     => ['example.com'],
            'relative path' => ['/relative/path'],
            'empty string'  => [''],
        ];
    }
}
