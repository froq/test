<?php declare(strict_types=1);
namespace test\froq\datetime\format;
use froq\datetime\format\Format;

class FormatTest extends \TestCase
{
    function testConstructor() {
        $format = new Format();
        $this->assertEquals('', $format); // Stringable.
        $this->assertInstanceOf(\Stringable::class, $format);
    }

    function testPatternMethods() {
        $format = new Format();
        $this->assertEquals('', $format->getPattern());

        $format->setPattern('Y-m-d');
        $this->assertSame('Y-m-d', $format->getPattern());
    }

    function testStringCast() {
        $format = new Format(Format::ISO);
        $this->assertSame(Format::ISO, (string) $format);
        $this->assertEquals(Format::ISO, $format); // Stringable.
    }
}
