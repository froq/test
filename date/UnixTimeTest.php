<?php declare(strict_types=1);
namespace test\froq\date;
use froq\date\UnixTime;

class UnixTimeTest extends \TestCase
{
    function test_now() {
        $this->assertSame(time(), UnixTime::now());
    }

    function test_from() {
        $when = '1990-01-09 23:30:11.506001 +00:00';
        $this->assertSame(631927811, UnixTime::from($when));
        $this->assertSame(strtotime($when), UnixTime::from($when));
    }
}
