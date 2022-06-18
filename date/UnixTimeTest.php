<?php declare(strict_types=1);
namespace test\froq\date;
use froq\date\{UnixTime, Date};
use DateTime;

class UnixTimeTest extends \TestCase
{
    function test_now() {
        $this->assertSame(time(), UnixTime::now());
    }

    function test_from() {
        $when = '1990-01-09 23:30:11.506001 +00:00';
        $time = strtotime($when);

        $this->assertSame($time, UnixTime::from($when));
        $this->assertSame(631927811, UnixTime::from($when));

        $this->assertSame($time, UnixTime::from(new Date($when)));
        $this->assertSame($time, UnixTime::from(new DateTime($when)));
    }
}
