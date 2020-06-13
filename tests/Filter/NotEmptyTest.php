<?php

namespace PHPModelGenerator\Tests\Filter;

use PHPModelGenerator\Filter\NotEmpty;
use PHPUnit\Framework\TestCase;

/**
 * Class NotEmptyTest
 *
 * @package PHPModelGenerator\Tests\Filter
 */
class NotEmptyTest extends TestCase
{
    /**
     * @dataProvider notEmptyDataProvider
     *
     * @param array|null $input
     * @param array|null $output
     */
    public function testNotEmptyFilter(?array $input, ?array $output): void
    {
        $this->assertSame($output, NotEmpty::filter($input));
    }

    public function notEmptyDataProvider(): array
    {
        return [
            'null' => [null, null],
            'empty array' => [[], []],
            'string array' => [['', 'Hello', null, '123'], ['Hello', '123']],
            'numeric array' => [[12, 0, 43], [12, 43]],
            'nested array' => [[['Hello'], [], [12], ['']], [['Hello'], [12], ['']]],
        ];
    }
}
