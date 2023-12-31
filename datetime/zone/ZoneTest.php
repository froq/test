<?php declare(strict_types=1);
namespace test\froq\datetime\zone;
use froq\datetime\zone\{Zone, ZoneException, ZoneList, ZoneIdList};
use froq\datetime\DateTimeZone;

class ZoneTest extends \TestCase
{
    function testConstructor() {
        $zone = new Zone('UTC');
        $this->assertSame('UTC', $zone->getId());

        $zone = new Zone('+00:00');
        $this->assertSame('+00:00', $zone->getId());

        $this->expectException(ZoneException::class);
        $this->expectExceptionMessage('Unknown or bad timezone (invalid)');
        new Zone('invalid');
    }

    function testMagicString() {
        $zone = new Zone('UTC');
        $this->assertSame('UTC', (string) $zone);
        $this->assertEquals('UTC', $zone); // Stringable.
    }

    function testGetters() {
        $zone = new Zone('UTC');
        $this->assertSame('UTC', $zone->getId());
        $this->assertSame('UTC', $zone->getName());
        $this->assertSame(0, $zone->getOffset());
        $this->assertSame('+00:00', $zone->getOffsetCode());

        $zone = new Zone('+00:00');
        $this->assertSame('+00:00', $zone->getId());
        $this->assertSame('+00:00', $zone->getName());
        $this->assertSame(0, $zone->getOffset());
        $this->assertSame('+00:00', $zone->getOffsetCode());
    }

    function testConverters() {
        $zone = new Zone('UTC');
        $this->assertEquals(new DateTimeZone('UTC'), $zone->toDateTimeZone());
        $this->assertInstanceOf(DateTimeZone::class, $zone->toDateTimeZone());
        $this->assertInstanceOf(\DateTimeZone::class, $zone->toDateTimeZone());

        $this->assertEquals([
            'id' => 'UTC', 'name' => 'UTC',
            'offset' => 0, 'offsetCode' => '+00:00',
        ], $zone->toArray());
    }

    function testList() {
        $zones = Zone::list();
        $this->assertSame('UTC', $zones[0]->getName());
        $this->assertSame(count(\DateTimeZone::listIdentifiers()), count($zones));
        $this->assertInstanceOf(ZoneList::class, $zones);
        $this->assertInstanceOf(\ItemList::class, $zones);
    }

    function testListIds() {
        $zoneIds = Zone::listIds();
        $this->assertSame('UTC', $zoneIds[0]->getName());
        $this->assertSame(count(\DateTimeZone::listIdentifiers()), count($zoneIds));
        $this->assertInstanceOf(ZoneIdList::class, $zoneIds);
        $this->assertInstanceOf(\ItemList::class, $zoneIds);
    }

    function testNormalizeId() {
        $this->assertSame('UTC', Zone::normalizeId('UTC'));
        $this->assertSame('Europe/Istanbul', Zone::normalizeId('EUROPE/ISTANBUL'));
    }

    function testValidateId() {
        $this->assertTrue(Zone::validateId('UTC'));
        $this->assertTrue(Zone::validateId('Europe/Istanbul'));

        $this->assertFalse(Zone::validateId('Z'));
        $this->assertFalse(Zone::validateId('GMT'));
        $this->assertFalse(Zone::validateId('+00:00'));
        $this->assertFalse(Zone::validateId('invalid'));
    }

    function testDefaultMethods() {
        $this->assertSame('UTC', Zone::default());
        $this->assertSame(0, Zone::defaultOffset());
        $this->assertSame('Europe/Istanbul', Zone::default('Europe/Istanbul'));
        $this->assertSame(10800, Zone::defaultOffset());
        Zone::default('UTC'); // Restore.

        try {
            Zone::default('');
        } catch (ZoneException $e) {
            $this->assertSame("Empty time zone id", $e->getMessage());
        }

        try {
            Zone::default('invalid');
        } catch (ZoneException $e) {
            $this->assertSame("Invalid time zone id: 'invalid' (use UTC or Xxx/Xxx format)", $e->getMessage());
        }
    }
}
