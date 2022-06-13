<?php declare(strict_types=1);
namespace test\froq\date;
use froq\date\{UtcDate, Date};

class UtcDateTest extends \TestCase
{
    function test_constructor() {
        $date = new UtcDate($when = gmdate('c'));
        $this->assertSame($when, $date->format('c'));
        $this->assertSame('UTC', $date->getTimezone());
        $this->assertInstanceOf(Date::class, $date);

        $date = new UtcDate($when = time());
        $this->assertSame($when, (int) $date->format('U'));

        $date = new UtcDate($when = microtime(true));
        $this->assertSame($when, (float) $date->format('U.u'));
    }
}
