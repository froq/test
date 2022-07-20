<?php declare(strict_types=1);
namespace test\froq\datetime\zone;
use froq\datetime\zone\{ZoneList, ZoneException, Zone};

class ZoneListTest extends \TestCase
{
    function test_constructor() {
        $zoneList = new ZoneList('UTC');
        $this->assertCount(1, $zoneList);
        $this->assertInstanceOf(Zone::class, $zoneList[0]);

        $zoneList = new ZoneList('country', 'tr');
        $this->assertCount(1, $zoneList);
        $this->assertInstanceOf(Zone::class, $zoneList[0]);

        $this->expectException(ZoneException::class);
        $this->expectExceptionMessage("Invalid group 'foo'");
        new ZoneList('foo');
    }

    function test_toArray() {
        $zoneList = new ZoneList();
        $this->assertSame(count(\DateTimeZone::listIdentifiers()), count($zoneList->toArray()));
    }
}
