<?php declare(strict_types=1);
namespace test\froq\datetime;
use froq\datetime\{UtcDateTime, DateTime, DateTimeZone};

class UtcDateTimeTest extends \TestCase
{
    function testConstructor() {
        $dt = new UtcDateTime();
        $this->assertInstanceOf(\DateTime::class, $dt);
        $this->assertInstanceOf(\Stringable::class, $dt);

        $dt = new UtcDateTime($when = gmdate('c'));
        $this->assertSame($when, $dt->toString('c'));
        $this->assertSame('UTC', $dt->getTimezone()->toString());
        $this->assertEquals('UTC', $dt->getTimezone()); // Stringable.
        $this->assertEquals(new DateTimeZone('UTC'), $dt->getTimezone());
        $this->assertInstanceOf(DateTime::class, $dt);

        $dt = new UtcDateTime($when = time());
        $this->assertSame($when, (int) $dt->format('U'));

        $dt = new UtcDateTime($when = microtime(true));
        $this->assertSame($when, (float) $dt->format('U.u'));
    }
}
