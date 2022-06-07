<?php declare(strict_types=1);
namespace froq\test\date;
use froq\date\{DateUtil, Diff};

class DateUtilTest extends \TestCase
{
    function test_ago() {
        $this->assertSame('Just now', DateUtil::ago('1 minute'));
        $this->assertSame('1 minute', DateUtil::ago('-1 minute'));
        $this->assertSame('59 minutes', DateUtil::ago('1 hour'));
        $this->assertSame('1 hour', DateUtil::ago('-1 hour'));
    }


    function test_diff() {
        $date1 = '1990-01-09 23:30:11.506001 +00:00';
        $date2 = '1990-02-19 13:22:45.389718 +00:00';

        $diff = DateUtil::diff($date1, $date2);

        $this->assertInstanceOf(Diff::class, $diff);
        $this->assertEquals($diff, new Diff(
            year: 0, month: 1, day: 9, days: 40,
            hour: 13, minute: 52, second: 33, microsecond: 883717
        ));

        $this->assertSame($diff->year, 0);
        $this->assertSame($diff->month, 1);
        $this->assertSame($diff->day, 9);
        $this->assertSame($diff->days, 40);
        $this->assertSame($diff->hour, 13);
        $this->assertSame($diff->minute, 52);
        $this->assertSame($diff->second, 33);
        $this->assertSame($diff->microsecond, 883717);

        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Cannot modify readonly property froq\date\Diff::$year');
        $diff->year = 1;
    }
}
