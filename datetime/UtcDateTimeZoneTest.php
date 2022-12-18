<?php declare(strict_types=1);
namespace test\froq\datetime;
use froq\datetime\{UtcDateTimeZone, DateTimeZone};

class UtcDateTimeZoneTest extends \TestCase
{
    function testConstructor() {
        $dtz = new UtcDateTimeZone();
        $this->assertInstanceOf(DateTimeZone::class, $dtz);
        $this->assertInstanceOf(\DateTimeZone::class, $dtz);
        $this->assertInstanceOf(\Stringable::class, $dtz);
    }

    function testStringCast() {
        $dtz = new UtcDateTimeZone();
        $this->assertSame('UTC', (string) $dtz);
        $this->assertEquals('UTC', $dtz); // Stringable.
    }
}
