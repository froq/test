<?php declare(strict_types=1);
namespace test\froq\date;
use froq\date\{UnixTime, UnixTimeException, Date};

class UnixTimeTest extends \TestCase
{
    function test_constructor() {
        $when = '1990-01-09 23:30:11 +00:00';
        $time = 631927811;

        $unixTime = new UnixTime($when);
        $this->assertSame($time, $unixTime->getTime());

        $unixTime = new UnixTime($time);
        $this->assertSame($time, $unixTime->getTime());

        $unixTime = new UnixTime();
        $this->assertSame(null, $unixTime->getTime());
        $unixTime->setTime($time);
        $this->assertSame($time, $unixTime->getTime());

        $this->expectException(UnixTimeException::class);
        $this->expectExceptionMessage('Invalid time: foo');
        new UnixTime('foo');
    }

    function test_now() {
        $this->assertSame(time(), UnixTime::now());
    }

    function test_make() {
        $when = '1990-01-09 23:30:11 +00:00';
        $time = 631927811;

        $this->assertSame($time, UnixTime::make(1990, 1, 9, 23, 30, 11));
    }

    function test_convert() {
        $when = '1990-01-09 23:30:11.506001 +00:00';
        $time = 631927811;

        $this->assertSame($time, UnixTime::convert($when));

        $this->assertSame($time, UnixTime::convert(new Date($when)));
        $this->assertSame($time, UnixTime::convert(new \DateTime($when)));
    }
}
