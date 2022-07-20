<?php declare(strict_types=1);
namespace test\froq\datetime\zone;
use froq\datetime\zone\{ZoneIdList, ZoneException, ZoneId};

class ZoneIdListTest extends \TestCase
{
    function test_constructor() {
        $zoneIdList = new ZoneIdList('UTC');
        $this->assertCount(1, $zoneIdList);
        $this->assertInstanceOf(ZoneId::class, $zoneIdList[0]);

        $zoneIdList = new ZoneIdList('country', 'tr');
        $this->assertCount(1, $zoneIdList);
        $this->assertInstanceOf(ZoneId::class, $zoneIdList[0]);

        $this->expectException(ZoneException::class);
        $this->expectExceptionMessage("Invalid group 'foo'");
        new ZoneIdList('foo');
    }

    function test_toArray() {
        $zoneIdList = new ZoneIdList();
        $this->assertSame(count(\DateTimeZone::listIdentifiers()), count($zoneIdList->toArray()));
    }
}
