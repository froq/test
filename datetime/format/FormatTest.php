<?php declare(strict_types=1);
namespace test\froq\datetime\format;
use froq\datetime\format\Format;

class FormatTest extends \TestCase
{
    function test_constructor() {
        $format = new Format();
        $this->assertEquals('', $format); // Stringable.
        $this->assertInstanceOf(\Stringable::class, $format);
    }

    function test_patternMethods() {
        $format = new Format();
        $this->assertEquals('', $format->getPattern());

        $format->setPattern('Y-m-d');
        $this->assertSame('Y-m-d', $format->getPattern());
    }

    function test_stringCast() {
        $format = new Format(Format::ISO);
        $this->assertSame(Format::ISO, (string) $format);
        $this->assertEquals(Format::ISO, $format); // Stringable.
    }
}
