<?php declare(strict_types=1);
namespace test\froq\date;
use froq\date\{TimeZone, TimeZoneException, TimeZoneInfo};

class TimeZoneTest extends \TestCase
{
    function test_construction() {
        $zone = new TimeZone('UTC');
        $this->assertSame('UTC', $zone->getId());

        $zone = new TimeZone('+00:00');
        $this->assertSame('+00:00', $zone->getId());

        $this->expectException(TimeZoneException::class);
        $this->expectExceptionMessageMatches('~^Invalid time zone id~');
        new TimeZone('invalid');
    }

    function test_infoMethods() {
        $zone = new TimeZone('UTC');
        $this->assertSame('UTC', $zone->getId());
        $this->assertSame('UTC', $zone->getName());
        $this->assertSame(0, $zone->getOffset());
        $this->assertSame('+00:00', $zone->getOffsetCode());

        $zone = new TimeZone('+00:00');
        $this->assertSame('+00:00', $zone->getId());
        $this->assertSame('+00:00', $zone->getName());
        $this->assertSame(0, $zone->getOffset());
        $this->assertSame('+00:00', $zone->getOffsetCode());
    }

    function test_makeMethods() {
        $zone = TimeZone::make('UTC');
        $this->assertSame('UTC', $zone->getName());
        $this->assertInstanceOf(\DateTimeZone::class, $zone);

        $info = TimeZone::makeInfo('UTC');
        $this->assertEquals(new TimeZoneInfo(
            id: 'UTC', name: 'UTC',
            offset: 0, offsetCode: '+00:00',
        ), $info);
    }
}
