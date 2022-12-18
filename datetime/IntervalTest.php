<?php declare(strict_types=1);
namespace test\froq\datetime;
use froq\datetime\Interval;

class IntervalTest extends \TestCase
{
    function testConstructor() {
        $interval = new Interval();
        $this->assertInstanceOf(\DateInterval::class, $interval);

        $interval = new Interval();
        foreach (['y', 'm', 'd', 'h', 'i', 's', 'f'] as $prop) {
            $this->assertEquals(0, $interval->$prop);
        }

        $interval = new Interval('P1Y1M1DT1H1M1S');
        foreach (['y', 'm', 'd', 'h', 'i', 's'] as $prop) {
            $this->assertEquals(1, $interval->$prop);
        }

        // Via other interval instance.
        $this->assertEquals(new Interval($interval), $interval);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unknown or bad format (invalid)');
        new Interval('invalid');
    }

    function testGetters() {
        $interval = new Interval('P1Y2M3DT1H2M3S');

        $this->assertSame(1, $interval->getYear());
        $this->assertSame(2, $interval->getMonth());
        $this->assertSame(3, $interval->getDay());
        $this->assertSame(1, $interval->getHour());
        $this->assertSame(2, $interval->getMinute());
        $this->assertSame(3, $interval->getSecond());
        $this->assertSame(0, $interval->getMicrosecond());
        $this->assertSame(0.0, $interval->getFraction());
        $this->assertSame(false, $interval->getDays()); // No diff call.
    }

    function testOf() {
        $interval = Interval::of(
            year: 0, month: 1, day: 9,
            hour: 13, minute: 52, second: 33,
            fraction: 0.883717, days: null,
        );

        $this->assertSame(0, $interval->getYear());
        $this->assertSame(1, $interval->getMonth());
        $this->assertSame(9, $interval->getDay());
        $this->assertSame(13, $interval->getHour());
        $this->assertSame(52, $interval->getMinute());
        $this->assertSame(33, $interval->getSecond());
        $this->assertSame(883717, $interval->getMicrosecond());
        $this->assertSame(0.883717, $interval->getFraction());
        $this->assertSame(false, $interval->getDays()); // No diff call.
    }

    function testOfDate() {
        $interval = Interval::ofDate(
            '0 year + 1 month + 9 day + '.
            '13 hour + 52 minute + 33 second'
        );

        $this->assertSame(0, $interval->getYear());
        $this->assertSame(1, $interval->getMonth());
        $this->assertSame(9, $interval->getDay());
        $this->assertSame(13, $interval->getHour());
        $this->assertSame(52, $interval->getMinute());
        $this->assertSame(33, $interval->getSecond());
        $this->assertSame(0, $interval->getMicrosecond());
        $this->assertSame(0.0, $interval->getFraction());
        $this->assertSame(false, $interval->getDays()); // No diff call.
    }

    function testToArray() {
        $interval = Interval::of(
            year: 0, month: 1, day: 9,
            hour: 13, minute: 52, second: 33,
            fraction: 0.883717, days: null,
        );

        $this->assertEquals($interval->toArray(), [
            'year' => 0, 'month' => 1, 'day' => 9,
            'hour' => 13, 'minute' => 52, 'second' => 33,
            'fraction' => 0.883717, 'days' => false, // No diff call.
        ]);
    }
}
