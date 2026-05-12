<?php

declare(strict_types=1);

namespace PHPModelGenerator\Tests\Format;

use PHPModelGenerator\Format\UriTemplateFormatValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UriTemplateFormatValidatorTest extends TestCase
{
    #[DataProvider('validProvider')]
    public function testValidUriTemplate(string $input): void
    {
        $this->assertTrue(UriTemplateFormatValidator::validate($input));
    }

    public static function validProvider(): array
    {
        return [
            'simple variable'      => ['/users/{id}'],
            'multiple variables'   => ['/users/{id}/posts/{postId}'],
            'reserved expansion'   => ['/path{+var}'],
            'query expansion'      => ['/search{?q,lang}'],
            'no template'          => ['/plain/path'],
            'empty string'         => [''],
        ];
    }

    #[DataProvider('invalidProvider')]
    public function testInvalidUriTemplate(string $input): void
    {
        $this->assertFalse(UriTemplateFormatValidator::validate($input));
    }

    public static function invalidProvider(): array
    {
        return [
            'unclosed brace'  => ['/users/{id'],
            'spaces'          => ['/path with spaces'],
        ];
    }
}
