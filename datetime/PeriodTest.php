<?php declare(strict_types=1);
namespace test\froq\datetime;
use froq\datetime\{Period, Interval};

class PeriodTest extends \TestCase
{
    function testConstructor() {
        $period = new Period('R4/2012-07-01T00:00:00Z/P7D');
        $this->assertInstanceOf(\DatePeriod::class, $period);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unknown or bad format (invalid)');
        new Period('invalid');
    }

    function testGetters() {
        $period = new Period('R4/2012-07-01T00:00:00Z/P7D');
        $this->assertInstanceOf(Interval::class, $period->getInterval());
        $this->assertInstanceOf(Interval::class, $period->getDateInterval());
        $this->assertInstanceOf(\DateInterval::class, $period->getInterval());
        $this->assertInstanceOf(\DateInterval::class, $period->getDateInterval());
        $this->assertEquals($period->getInterval(), $period->getDateInterval());
    }

    function testToArray() {
        $period = new Period('R4/2012-07-01T00:00:00Z/P7D');
        $this->assertCount(5, $period->toArray());
        $this->assertInstanceOf(\DateTime::class, $period->toArray()[0]);
    }
}
