<?php declare(strict_types=1);
namespace test\froq\datetime\zone;
use froq\datetime\zone\{Zone, ZoneList, ZoneId, ZoneIdList};
use froq\datetime\DateTimeZone;

class ZoneIdTest extends \TestCase
{
    function testConstructor() {
        $zoneId = new ZoneId('UTC');
        $this->assertSame('UTC', $zoneId->id);
        $this->assertSame('UTC', $zoneId->name);

        $zoneId = new ZoneId('Europe/Istanbul');
        $this->assertSame('Europe/Istanbul', $zoneId->id);
        $this->assertSame('Europe / Istanbul', $zoneId->name);
    }

    function testMagicString() {
        $zoneId = new ZoneId('UTC');
        $this->assertSame('UTC', (string) $zoneId);
        $this->assertEquals('UTC', $zoneId); // Stringable.
    }

    function testGetters() {
        $zoneId = new ZoneId('UTC');
        $this->assertSame('UTC', $zoneId->getId());
        $this->assertSame('UTC', $zoneId->getName());

        $zoneId = new ZoneId('Europe/Istanbul');
        $this->assertSame('Europe/Istanbul', $zoneId->getId());
        $this->assertSame('Europe / Istanbul', $zoneId->getName());
    }

    function testConverters() {
        $zoneId = new ZoneId('UTC');
        $this->assertEquals(new Zone('UTC'), $zoneId->toZone());
        $this->assertInstanceOf(Zone::class, $zoneId->toZone());
        $this->assertEquals(new DateTimeZone('UTC'), $zoneId->toDateTimeZone());
        $this->assertInstanceOf(\DateTimeZone::class, $zoneId->toDateTimeZone());

        $this->assertEquals([
            'id' => 'UTC', 'name' => 'UTC',
        ], $zoneId->toArray());
    }
}
