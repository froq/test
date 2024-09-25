<?php declare(strict_types=1);
namespace test\froq\datetime;
use froq\datetime\{Period, Interval};

class PeriodTest extends \TestCase
{
    function testConstructor() {
        $period = new Period('R4/2012-07-01T00:00:00Z/P7D');
        $this->assertInstanceOf(\DatePeriod::class, $period);

        $this->assertTrue($period->startDateIncluded());
        $this->assertTrue($period->endDateIncluded());

        $this->expectException(\Exception::class);
        new Period('invalid');
    }

    function testGetters() {
        $period = new Period('R4/2012-07-01T00:00:00Z/P7D');
        $this->assertInstanceOf(Interval::class, $period->getInterval());
        $this->assertInstanceOf(\DateInterval::class, $period->getInterval());
        $this->assertEquals($period->getInterval(), $period->getDateInterval());
    }

    function testToArray() {
        $period = new Period('R4/2012-07-01T00:00:00Z/P7D');
        $this->assertCount(6, $period->toArray());
    }

    function testLength() {
        $period = new Period('R4/2012-07-01T00:00:00Z/P7D');
        $this->assertSame(6, $period->length());
    }
}
