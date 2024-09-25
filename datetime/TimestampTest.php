<?php declare(strict_types=1);
namespace test\froq\datetime;
use froq\datetime\{Timestamp, TimestampException, DateTime};

class TimestampTest extends \TestCase
{
    function testConstructor() {
        $ts = new Timestamp(); // Default=now.
        $this->assertSame(time(), $ts->getTime());

        $when = '1990-01-09 23:30:11 +00:00';
        $time = 631927811;

        $ts = new Timestamp($when);
        $this->assertSame($time, $ts->getTime());

        $ts = new Timestamp($time);
        $this->assertSame($time, $ts->getTime());

        $ts = new Timestamp($time + 0.123456); // Int cast.
        $this->assertSame($time, $ts->getTime());

        $ts = new Timestamp(new DateTime($when));
        $this->assertSame($time, $ts->getTime());

        try {
            new Timestamp('');
        } catch (TimestampException $e) {
            $this->assertSame("Invalid date/time: ''", $e->getMessage());
        }

        try {
            new Timestamp(null);
        } catch (TimestampException $e) {
            $this->assertSame("Invalid date/time: null", $e->getMessage());
        }

        try {
            new Timestamp('foo');
        } catch (TimestampException $e) {
            $this->assertSame("Invalid date/time: 'foo'", $e->getMessage());
        }
    }

    function testAccessMethods() {
        $ts = new Timestamp();
        $this->assertSame($time = time(), $ts->getTime());
        $this->assertSame($time, $ts->setTime($time)->getTime());
    }

    function testFormatMethods() {
        $ts = new Timestamp();
        $this->assertSame(date('YmdHis', $ts->getTime()), $ts->format('YmdHis'));
        $this->assertSame(gmdate('YmdHis', $ts->getTime()), $ts->formatUtc('YmdHis'));
    }

    function testToFromDateTime() {
        $ts = new Timestamp();
        $this->assertInstanceOf(\DateTime::class, $ts->toDateTime());
        $this->assertInstanceOf(DateTime::class, $ts->toDateTime());
        $this->assertSame('', $ts->toDateTime()->getTimezone()->getId());
        $this->assertSame('UTC', $ts->toDateTime('UTC')->getTimezone()->getId());

        $this->assertInstanceOf(Timestamp::class, Timestamp::fromDateTime(gmdate('c')));
        $this->assertInstanceOf(Timestamp::class, Timestamp::fromDateTime(new DateTime()));
    }

    function testOfMethods() {
        $ts1 = new Timestamp('1990-01-09 23:30:11 +00:00');
        $ts2 = new Timestamp(631927811);

        $this->assertEquals($ts1, Timestamp::of(1990, 1, 9, 23, 30, 11));
        $this->assertEquals($ts2, Timestamp::ofUtc(1990, 1, 9, 23, 30, 11));
    }

    function testConvert() {
        $when = '1990-01-09 23:30:11.506001 +00:00';
        $time = 631927811;

        $this->assertSame($time, Timestamp::convert($when));
        $this->assertSame($time, Timestamp::convert(new DateTime($when)));
        $this->assertSame($time, Timestamp::convert(new \DateTime($when)));
        $this->assertNull(Timestamp::convert('invalid'));
    }

    function testNow() {
        $this->assertSame(time(), Timestamp::now());
    }
}
