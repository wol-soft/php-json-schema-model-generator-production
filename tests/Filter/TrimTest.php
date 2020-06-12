<?php

namespace PHPModelGenerator\Tests\Filter;

use PHPModelGenerator\Filter\Trim;
use PHPUnit\Framework\TestCase;

/**
 * Class TrimTest
 *
 * @package PHPModelGenerator\Tests\Filter
 */
class TrimTest extends TestCase
{
    /**
     * @dataProvider trimDataProvider
     *
     * @param string|null $input
     * @param string|null $output
     */
    public function testTrimFilter(?string $input, ?string $output): void
    {
        $this->assertSame($output, Trim::filter($input));
    }

    public function trimDataProvider(): array
    {
        return [
            'null' => [null, null],
            'empty string' => ['', ''],
            'String with whitespaces only' => [" \n \r \t ", ''],
            'Numeric string' => ['  123   ', '123'],
            'Sentence' => ['   Hello  World  ', 'Hello  World'],
            'Nothing to do' => ['Hi there', 'Hi there'],
        ];
    }
}
