<?php declare(strict_types=1);
namespace test\froq\date;
use froq\date\{Dater, Date, UtcDate, DateException};

class DaterTest extends \TestCase
{
    function test_constructor() {
        $when = '1990-01-09 23:30:11 +00:00';

        ['year' => $year, 'month' => $month, 'day' => $day,
         'hour' => $hour, 'minute' => $minute , 'second' => $second
        ] = Date::parse($when);

        $dater = new Dater(
            year: $year, month: $month, day: $day,
            hour: $hour, minute: $minute, second: $second,
        );

        $this->assertSame($when, $dater->format('Y-m-d H:i:s P'));
    }

    function test_settersGetters() {
        $dater = new Dater();

        $this->assertNull($dater->getYear());
        $this->assertNull($dater->getMonth());
        $this->assertNull($dater->getDay());
        $this->assertNull($dater->getHour());
        $this->assertNull($dater->getMinute());
        $this->assertNull($dater->getSecond());
        $this->assertNull($dater->getMicrosecond());
        $this->assertNull($dater->getTimezone());
        $this->assertNull($dater->getLocale());

        $dater->setYear(1990)
              ->setMonth(1)
              ->setDay(9)
              ->setHours(23)
              ->setMinutes(30)
              ->setSeconds(11)
              ->setMicroseconds(506001)
              ->setTimezone('UTC')
              ->setLocale('en_US')
        ;

        $this->assertSame(1990, $dater->getYear());
        $this->assertSame(1, $dater->getMonth());
        $this->assertSame(9, $dater->getDay());
        $this->assertSame(23, $dater->getHour());
        $this->assertSame(30, $dater->getMinute());
        $this->assertSame(11, $dater->getSecond());
        $this->assertSame(506001, $dater->getMicrosecond());
        $this->assertSame('UTC', $dater->getTimezone());
        $this->assertSame('en_US', $dater->getLocale());
    }

    function test_converters() {
        $dater = new Dater();

        $this->assertInstanceOf(Date::class, $dater->toDate());
        $this->assertInstanceOf(UtcDate::class, $dater->toUtcDate());
    }

    function test_format() {
        $dater = new Dater();

        $format = 'Y-m-d H:i:s';
        $this->assertSame(date($format), $dater->format($format));
        $this->assertSame(gmdate($format), $dater->formatUtc($format));
    }

    function test_setDate() {
        $dater = new Dater();
        $dater->setDate('1990-01-09');

        $this->assertSame('1990-01-09', $dater->getDate());
        $this->assertSame('1990-01-09 00:00:00.000000', $dater->getFullDate());

        $this->expectException(DateException::class);
        $this->expectExceptionMessage('Invalid date: foo');
        $dater->setDate('foo');
    }

    function test_getDate() {
        $dater = new Dater(1990, 1, 9, 23, 30, 11, 506001);

        $this->assertSame('1990-01-09', $dater->getDate());
        $this->assertSame('1990-01-09 23:30:11.506001', $dater->getFullDate());
    }

    function test_setTime() {
        $dater = new Dater();
        $dater->setTime('23:30:11');

        $this->assertSame('23:30', $dater->getTime());
        $this->assertSame('23:30:11.000000', $dater->getFullTime());

        $this->expectException(DateException::class);
        $this->expectExceptionMessage('Invalid time: foo');
        $dater->setTime('foo');
    }

    function test_getTime() {
        $dater = new Dater(1990, 1, 9, 23, 30, 11, 506001);

        $this->assertSame('23:30', $dater->getTime());
        $this->assertSame('23:30:11.506001', $dater->getFullTime());
    }

    function test_getTimestamp() {
        $dater = new Dater(1990, 1, 9, 23, 30, 11, 506001);

        $this->assertSame(631927811, $dater->getTimestamp());
        $this->assertSame(631927811.506001, $dater->getTimestamp(true));
    }
}
