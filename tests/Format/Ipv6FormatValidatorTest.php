<?php

declare(strict_types=1);

namespace PHPModelGenerator\Tests\Format;

use PHPModelGenerator\Format\Ipv6FormatValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class Ipv6FormatValidatorTest extends TestCase
{
    #[DataProvider('validProvider')]
    public function testValidIpv6(string $input): void
    {
        $this->assertTrue(Ipv6FormatValidator::validate($input));
    }

    public static function validProvider(): array
    {
        return [
            'full'         => ['2001:0db8:85a3:0000:0000:8a2e:0370:7334'],
            'compressed'   => ['2001:db8::1'],
            'loopback'     => ['::1'],
            'all zeros'    => ['::'],
            'mixed case'   => ['2001:DB8::1'],
        ];
    }

    #[DataProvider('invalidProvider')]
    public function testInvalidIpv6(string $input): void
    {
        $this->assertFalse(Ipv6FormatValidator::validate($input));
    }

    public static function invalidProvider(): array
    {
        return [
            'ipv4 address'    => ['192.168.1.1'],
            'too many groups' => ['2001:db8:85a3:0:0:8a2e:370:7334:extra'],
            'empty string'    => [''],
            'hostname'        => ['example.com'],
        ];
    }
}
