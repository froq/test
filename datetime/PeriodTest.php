<?php declare(strict_types=1);
namespace test\froq\datetime;
use froq\datetime\{Period, Interval};

class PeriodTest extends \TestCase
{
    function test_constructor() {
        $period = new Period('R4/2012-07-01T00:00:00Z/P7D');
        $this->assertInstanceOf(\DatePeriod::class, $period);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unknown or bad format (invalid)');
        new Period('invalid');
    }

    function test_getters() {
        $period = new Period('R4/2012-07-01T00:00:00Z/P7D');
        $this->assertInstanceOf(Interval::class, $period->getInterval());
        $this->assertInstanceOf(Interval::class, $period->getDateInterval());
        $this->assertInstanceOf(\DateInterval::class, $period->getInterval());
        $this->assertInstanceOf(\DateInterval::class, $period->getDateInterval());
        $this->assertEquals($period->getInterval(), $period->getDateInterval());
    }

    function test_toArray() {
        $period = new Period('R4/2012-07-01T00:00:00Z/P7D');
        $this->assertCount(5, $period->toArray());
        $this->assertInstanceOf(\DateTime::class, $period->toArray()[0]);
    }
}
