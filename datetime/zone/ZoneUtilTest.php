<?php declare(strict_types=1);
namespace test\froq\datetime\zone;
use froq\datetime\zone\{ZoneUtil, ZoneException};

class ZoneUtilTest extends \TestCase
{
    function test_listIds() {
        $ids = ZoneUtil::listIds('UTC');
        $this->assertCount(1, $ids);
        $this->assertInstanceOf(\Set::class, $ids);

        $ids = ZoneUtil::listIds('country', 'tr');
        $this->assertCount(1, $ids);
        $this->assertInstanceOf(\Set::class, $ids);

        $this->assertSame(count(\DateTimeZone::listIdentifiers()), count(ZoneUtil::listIds()));

        $this->expectException(ZoneException::class);
        $this->expectExceptionMessage("Invalid group 'foo'");
        ZoneUtil::listIds('foo');
    }

    function test_idToName() {
        $this->assertSame('UTC', ZoneUtil::idToName('UTC'));
        $this->assertSame('Europe / Istanbul', ZoneUtil::idToName('Europe/Istanbul'));
    }

    function test_offsetToCode() {
        $this->assertSame('+00:00', ZoneUtil::offsetToCode(0));
        $this->assertSame('+03:00', ZoneUtil::offsetToCode(10800));
    }
}
