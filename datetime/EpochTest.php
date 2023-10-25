<?php declare(strict_types=1);
namespace test\froq\datetime;
use froq\datetime\{Epoch, EpochException, DateTime};

class EpochTest extends \TestCase
{
    function testConstructor() {
        $epoch = new Epoch(); // Default=now.
        $this->assertSame(time(), $epoch->getTime());

        $when = '1990-01-09 23:30:11 +00:00';
        $time = 631927811;

        $epoch = new Epoch($when);
        $this->assertSame($time, $epoch->getTime());

        $epoch = new Epoch($time);
        $this->assertSame($time, $epoch->getTime());

        $epoch = new Epoch($time + 0.123456); // Int cast.
        $this->assertSame($time, $epoch->getTime());

        $epoch = new Epoch(new DateTime($when));
        $this->assertSame($time, $epoch->getTime());

        try {
            new Epoch('');
        } catch (EpochException $e) {
            $this->assertSame("Invalid date/time: ''", $e->getMessage());
        }

        try {
            new Epoch(null);
        } catch (EpochException $e) {
            $this->assertSame("Invalid date/time: null", $e->getMessage());
        }

        try {
            new Epoch('foo');
        } catch (EpochException $e) {
            $this->assertSame("Invalid date/time: 'foo'", $e->getMessage());
        }
    }

    function testAccessMethods() {
        $epoch = new Epoch();
        $this->assertSame($time = time(), $epoch->getTime());
        $this->assertSame($time, $epoch->setTime($time)->getTime());
    }

    function testFormatMethods() {
        $epoch = new Epoch();
        $this->assertSame(date('YmdHis', $epoch->getTime()), $epoch->format('YmdHis'));
        $this->assertSame(gmdate('YmdHis', $epoch->getTime()), $epoch->formatUtc('YmdHis'));
    }

    function testOfMethods() {
        $epoch1 = new Epoch('1990-01-09 23:30:11 +00:00');
        $epoch2 = new Epoch(631927811);

        $this->assertEquals($epoch1, Epoch::of(1990, 1, 9, 23, 30, 11));
        $this->assertEquals($epoch2, Epoch::ofUtc(1990, 1, 9, 23, 30, 11));
    }

    function testConvert() {
        $when = '1990-01-09 23:30:11.506001 +00:00';
        $time = 631927811;

        $this->assertSame($time, Epoch::convert($when));
        $this->assertSame($time, Epoch::convert(new DateTime($when)));
        $this->assertSame($time, Epoch::convert(new \DateTime($when)));
        $this->assertNull(Epoch::convert('invalid'));
    }

    function testNow() {
        $this->assertSame(time(), Epoch::now());
    }
}
