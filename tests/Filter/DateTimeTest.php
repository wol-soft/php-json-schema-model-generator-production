<?php

namespace PHPModelGenerator\Tests\Filter;

use DateTime;
use Exception;
use PHPModelGenerator\Filter\DateTime as DateTimeFilter;
use PHPUnit\Framework\TestCase;

/**
 * Class TrimTest
 *
 * @package PHPModelGenerator\Tests\Filter
 */
class DateTimeTest extends TestCase
{
    /**
     * @dataProvider dateTimeDataProvider
     *
     * @param string|null $input
     * @param string|null $output
     */
    public function testDateTimeFilter(?string $input, ?string $output): void
    {
        $result = DateTimeFilter::filter($input);

        if ($output === null) {
            $this->assertNull($result);
        } else {
            $this->assertSame((new DateTime($output))->format(DATE_ATOM), $result->format(DATE_ATOM));
        }
    }

    public function dateTimeDataProvider(): array
    {
        return [
            'null' => [null, null],
            'Empty string' => ['', 'now'],
            'DateTime compatible string' => ['+1 day', '+1 day'],
            'DateTime' => ['2020-12-12', '2020-12-12'],
        ];
    }

    public function testInvalidDateTimeThrowsAnException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid Date Time value "Hello"');

        DateTimeFilter::filter("Hello");
    }

    public function testConvertNullToNowOption(): void
    {
        $this->assertSame(
            (new DateTime('now'))->format(DATE_ATOM),
            DateTimeFilter::filter(null, ['convertNullToNow' => true])->format(DATE_ATOM)
        );
    }

    public function testConvertEmptyValueToNull(): void
    {
        $this->assertNull(DateTimeFilter::filter('', ['convertEmptyValueToNull' => true]));
    }

    public function testDenyEmptyValueOption(): void
    {
        // check if null is accepted if the option is set
        $this->assertNull(DateTimeFilter::filter(null, ['denyEmptyValue' => true]));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid Date Time value ""');

        DateTimeFilter::filter('', ['denyEmptyValue' => true]);
    }

    public function testCreateFromFormatOption(): void
    {
        $this->assertSame(
            (new DateTime('2020-12-12'))->format(DATE_ATOM),
            DateTimeFilter::filter('12122020', ['createFromFormat' => '!mdY'])->format(DATE_ATOM)
        );
    }

    public function testCreateFromFormatOptionWithFormatConstant(): void
    {
        $dateTime = new DateTime();
        $this->assertSame(
            $dateTime->format(DATE_ATOM),
            DateTimeFilter::filter($dateTime->format(DATE_RSS), ['createFromFormat' => 'RSS'])->format(DATE_ATOM)
        );
    }

    public function testSerializeDefaultFormat(): void
    {
        $this->assertNull(DateTimeFilter::serialize(null));

        $dateTime = new DateTime();
        $this->assertSame($dateTime->format(DATE_ISO8601), DateTimeFilter::serialize($dateTime));
    }

    public function testSerializeWithCreateFromFormat(): void
    {
        $dateTime = new DateTime();

        $this->assertSame(
            $dateTime->format('mdY'),
            DateTimeFilter::serialize($dateTime, ['createFromFormat' => 'mdY'])
        );
    }

    public function testSerializeWithOutputFormat(): void
    {
        $dateTime = new DateTime();

        $this->assertSame(
            $dateTime->format('Ymd'),
            DateTimeFilter::serialize($dateTime, ['createFromFormat' => 'mdY', 'outputFormat' => 'Ymd'])
        );
    }

    public function testSerializeWithCreateFromFormatWithFormatConstant(): void
    {
        $dateTime = new DateTime();

        $this->assertSame(
            $dateTime->format(DATE_RSS),
            DateTimeFilter::serialize($dateTime, ['createFromFormat' => 'RSS'])
        );
    }

    public function testSerializeWithOutputFormatWithFormatConstant(): void
    {
        $dateTime = new DateTime();

        $this->assertSame(
            $dateTime->format(DATE_RSS),
            DateTimeFilter::serialize($dateTime, ['createFromFormat' => 'ATOM', 'outputFormat' => 'RSS'])
        );
    }
}
