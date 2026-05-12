<?php

declare(strict_types=1);

namespace PHPModelGenerator\Tests\Format;

use PHPModelGenerator\Format\UriFormatValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UriFormatValidatorTest extends TestCase
{
    #[DataProvider('validProvider')]
    public function testValidUri(string $input): void
    {
        $this->assertTrue(UriFormatValidator::validate($input));
    }

    public static function validProvider(): array
    {
        return [
            'http'         => ['http://example.com'],
            'https'        => ['https://example.com/path?q=1#frag'],
            'ftp'          => ['ftp://files.example.com/resource'],
            'with port'    => ['http://example.com:8080/path'],
        ];
    }

    #[DataProvider('invalidProvider')]
    public function testInvalidUri(string $input): void
    {
        $this->assertFalse(UriFormatValidator::validate($input));
    }

    public static function invalidProvider(): array
    {
        return [
            'relative path'   => ['/relative/path'],
            'no scheme'       => ['example.com'],
            'empty string'    => [''],
            'scheme only'     => ['http://'],
        ];
    }
}
