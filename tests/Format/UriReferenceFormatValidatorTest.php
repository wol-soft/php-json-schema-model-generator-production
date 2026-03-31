<?php

declare(strict_types=1);

namespace PHPModelGenerator\Tests\Format;

use PHPModelGenerator\Format\UriReferenceFormatValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UriReferenceFormatValidatorTest extends TestCase
{
    #[DataProvider('validProvider')]
    public function testValidUriReference(string $input): void
    {
        $this->assertTrue(UriReferenceFormatValidator::validate($input));
    }

    public static function validProvider(): array
    {
        return [
            'absolute uri'    => ['http://example.com/path'],
            'absolute path'   => ['/absolute/path'],
            'relative path'   => ['relative/path'],
            'fragment only'   => ['#fragment'],
            'query only'      => ['?query=1'],
            'empty string'    => [''],
        ];
    }

    #[DataProvider('invalidProvider')]
    public function testInvalidUriReference(string $input): void
    {
        $this->assertFalse(UriReferenceFormatValidator::validate($input));
    }

    public static function invalidProvider(): array
    {
        return [
            'invalid chars'   => ["path with spaces"],
            'null byte'       => ["\0"],
        ];
    }
}
