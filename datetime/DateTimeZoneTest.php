<?php declare(strict_types=1);
namespace test\froq\datetime;
use froq\datetime\{DateTimeZone, DateTimeZoneException};
use froq\datetime\zone\{Zone, ZoneId};

class DateTimeZoneTest extends \TestCase
{
    function testConstructor() {
        $dtz = new DateTimeZone('UTC');
        $this->assertInstanceOf(\DateTimeZone::class, $dtz);
        $this->assertInstanceOf(\Stringable::class, $dtz);

        $dtz = new DateTimeZone('UTC');
        $this->assertSame('UTC', $dtz->getName());

        $default = date_default_timezone_get();

        $dtz = new DateTimeZone('default');
        $this->assertSame($default, $dtz->getName());

        $dtz = new DateTimeZone('@default');
        $this->assertSame($default, $dtz->getName());

        try {
            new DateTimeZone('');
        } catch (DateTimeZoneException $e) {
            $this->assertSame('Empty time zone id', $e->getMessage());
        }

        try {
            new DateTimeZone('invalid');
        } catch (DateTimeZoneException $e) {
            $this->assertSame('Unknown or bad timezone (invalid)', $e->getMessage());
        }
    }

    function testMagicString() {
        $dtz = new DateTimeZone('UTC');
        $this->assertSame('UTC', (string) $dtz);
        $this->assertEquals('UTC', $dtz); // Stringable.
    }

    function testGetterMethods() {
        $dtz = new DateTimeZone('Europe/Istanbul');
        $this->assertSame('Europe/Istanbul', $dtz->getId());
        $this->assertSame('+03', $dtz->getAbbr());
        $this->assertSame(3, $dtz->getType());
        $this->assertSame([
            'country_code' => 'TR',
            'latitude' => 41.01666,
            'longitude' => 28.96666,
            'comments' => null
        ], $dtz->getLocation());
        $this->assertSame('Europe / Istanbul', $dtz->getDisplayName());
        $this->assertSame(10800, $dtz->getOffset());
        $this->assertSame('+03:00', $dtz->getOffsetCode());
    }

    function testZoneMethods() {
        $dtz = new DateTimeZone('UTC');
        $this->assertInstanceOf(Zone::class, $dtz->toZone());
        $this->assertInstanceOf(ZoneId::class, $dtz->toZoneId());
    }

    function testStringMethods() {
        $dtz = new DateTimeZone('UTC');
        $this->assertSame('UTC', $dtz->toString());
        $this->assertSame('"UTC"', json_encode($dtz));
    }

    function testBridgeMethods() {
        $this->assertSame('Europe/Istanbul', DateTimeZone::normalizeId('euroPE/IStaNBUL'));
        $this->assertTrue(DateTimeZone::validateId('Europe/Istanbul'));
        $this->assertFalse(DateTimeZone::validateId('invalid'));
        $this->assertSame('UTC', DateTimeZone::default());
        $this->assertSame(0, DateTimeZone::defaultOffset());
    }
}
