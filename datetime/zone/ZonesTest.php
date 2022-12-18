<?php declare(strict_types=1);
namespace test\froq\datetime\zone;
use froq\datetime\zone\{Zones, ZoneException, ZoneId};

class ZonesTest extends \TestCase
{
    function testConstants() {
        $this->assertSame('UTC', Zones::UTC);
        $this->assertSame('Europe/Istanbul', Zones::EUROPE_ISTANBUL);
        $this->assertCount(425, get_class_constants(Zones::class, false));
    }

    function testAll() {
        $all = Zones::all();
        $this->assertCount(425, $all);
        $this->assertSame('UTC', $all[0]);
    }
}
