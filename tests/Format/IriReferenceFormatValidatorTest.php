<?php

declare(strict_types=1);

namespace PHPModelGenerator\Tests\Format;

use PHPModelGenerator\Format\IriReferenceFormatValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class IriReferenceFormatValidatorTest extends TestCase
{
    #[DataProvider('validProvider')]
    public function testValidIriReference(string $input): void
    {
        $this->assertTrue(IriReferenceFormatValidator::validate($input));
    }

    public static function validProvider(): array
    {
        return [
            'absolute iri'       => ['http://example.com/München'],
            'relative ascii'     => ['/relative/path'],
            'relative unicode'   => ['/pfad/München'],
            'empty string'       => [''],
        ];
    }

    #[DataProvider('invalidProvider')]
    public function testInvalidIriReference(string $input): void
    {
        $this->assertFalse(IriReferenceFormatValidator::validate($input));
    }

    public static function invalidProvider(): array
    {
        return [
            'spaces'    => ['path with spaces'],
            'null byte' => ["\0"],
        ];
    }
}
