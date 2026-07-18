<?php

declare(strict_types=1);

namespace PHPModelGenerator\Tests\Exception;

use PHPModelGenerator\Exception\Arrays\InvalidItemException;
use PHPModelGenerator\Exception\Generic\DeniedPropertyException;
use PHPModelGenerator\Exception\MessageFormatter;
use PHPModelGenerator\Exception\Number\MinimumException;
use PHPModelGenerator\Exception\Object\RequiredValueException;
use PHPUnit\Framework\TestCase;

class MessageFormatterTest extends TestCase
{
    public function testFormatRendersScalarsAndStructuresAsJson(): void
    {
        $this->assertSame('"hello"', MessageFormatter::format('hello'));
        $this->assertSame('42', MessageFormatter::format(42));
        $this->assertSame('true', MessageFormatter::format(true));
        $this->assertSame('null', MessageFormatter::format(null));
        $this->assertSame('["a","b"]', MessageFormatter::format(['a', 'b']));
        $this->assertSame('{"city":"NY"}', MessageFormatter::format(['city' => 'NY']));
    }

    public function testFormatFallsBackToVarExportForNonEncodableValues(): void
    {
        $resource = fopen('php://memory', 'r');

        $this->assertSame(var_export($resource, true), MessageFormatter::format($resource));

        fclose($resource);
    }

    public function testPluralizeUsesTheSingularFormForACountOfOne(): void
    {
        $this->assertSame('1 element', MessageFormatter::pluralize(1, 'element'));
    }

    public function testPluralizeUsesTheDefaultPluralFormForAnyOtherCount(): void
    {
        $this->assertSame('0 elements', MessageFormatter::pluralize(0, 'element'));
        $this->assertSame('2 elements', MessageFormatter::pluralize(2, 'element'));
    }

    public function testPluralizeAcceptsAnIrregularPluralForm(): void
    {
        $this->assertSame('1 entry', MessageFormatter::pluralize(1, 'entry', 'entries'));
        $this->assertSame('3 entries', MessageFormatter::pluralize(3, 'entry', 'entries'));
    }

    public function testQuotedListJoinsWithCommaByDefault(): void
    {
        $this->assertSame("'a', 'b', 'c'", MessageFormatter::quotedList(['a', 'b', 'c']));
    }

    public function testQuotedListAcceptsACustomGlue(): void
    {
        $this->assertSame("'a'\n  - 'b'\n  - 'c'", MessageFormatter::quotedList(['a', 'b', 'c'], "\n  - "));
    }

    public function testQuotedListOfASingleItemHasNoGlue(): void
    {
        $this->assertSame("'a'", MessageFormatter::quotedList(['a']));
    }

    public function testQuotedListOfNoItemsIsEmpty(): void
    {
        $this->assertSame('', MessageFormatter::quotedList([]));
    }

    public function testBulletListJoinsMultipleSiblingExceptionMessages(): void
    {
        $exceptions = [
            new RequiredValueException('', 'name', '/properties/name'),
            new MinimumException(1, 'age', '/properties/age', 2),
        ];

        $this->assertSame(
            "Missing required value for 'name'\n    * Value for 'age' must not be smaller than 2",
            MessageFormatter::bulletList($exceptions),
        );
    }

    public function testBulletListOfASingleExceptionHasNoLeadingBullet(): void
    {
        $exceptions = [new DeniedPropertyException(1, 'flag', '/properties/flag')];

        $this->assertSame("Value for 'flag' is not allowed", MessageFormatter::bulletList($exceptions));
    }

    public function testBulletListIndentsAMultiLineSiblingMessageUnderItsOwnBullet(): void
    {
        $multiLine = new InvalidItemException(
            [0],
            'numbers',
            '/properties/numbers',
            [0 => [new MinimumException(0, 'numbers', '/properties/numbers/items/minimum', 1)]],
        );

        $exceptions = [
            new RequiredValueException('', 'name', '/properties/name'),
            $multiLine,
        ];

        $this->assertSame(
            "Missing required value for 'name'\n    * Invalid items in array 'numbers':" .
                "\n      - invalid item #0\n        * Value for 'numbers' must not be smaller than 1",
            MessageFormatter::bulletList($exceptions),
        );
    }

    public function testFlattenNestedMessageBulletsASingleLineMessage(): void
    {
        $nested = new DeniedPropertyException(1, 'flag', '/properties/flag');

        $this->assertSame(
            "Value for 'flag' is not allowed",
            MessageFormatter::flattenNestedMessage($nested->getMessage()),
        );
    }

    public function testFlattenNestedMessageReindentsAnAlreadyBulletedMessage(): void
    {
        $nested = new InvalidItemException(
            [0],
            'numbers',
            '/properties/numbers',
            [0 => [new MinimumException(0, 'numbers', '/properties/numbers/items/minimum', 1)]],
        );

        $this->assertSame(
            "Invalid items in array 'numbers':\n      - invalid item #0" .
                "\n        * Value for 'numbers' must not be smaller than 1",
            MessageFormatter::flattenNestedMessage($nested->getMessage()),
        );
    }
}
