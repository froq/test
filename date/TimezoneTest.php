<?php declare(strict_types=1);
namespace test\froq\date;
use froq\date\{Timezone, TimezoneException};

class TimezoneTest extends \TestCase
{
    function test_construction() {
        $zone = new Timezone('UTC');
        $this->assertSame('UTC', $zone->getId());

        $zone = new Timezone('+00:00');
        $this->assertSame('+00:00', $zone->getId());

        try {
            new Timezone('invalid');
        } catch (TimezoneException $e) {
            $this->assertStringContains('Invalid timezone id', $e->getMessage());
        }
    }

    function test_infoMethods() {
        $zone = new Timezone('UTC');
        $this->assertSame('UTC', $zone->getId());
        $this->assertSame('UTC', $zone->getName());
        $this->assertSame(0, $zone->getOffset());
        $this->assertSame('+00:00', $zone->getOffsetCode());

        $zone = new Timezone('+00:00');
        $this->assertSame('+00:00', $zone->getId());
        $this->assertSame('+00:00', $zone->getName());
        $this->assertSame(0, $zone->getOffset());
        $this->assertSame('+00:00', $zone->getOffsetCode());
    }

    function test_makeMethods() {
        $zone = Timezone::make('UTC');
        $this->assertSame('UTC', $zone->getName());
        $this->assertInstanceOf(\DateTimeZone::class, $zone);

        $info = Timezone::makeInfo('UTC');
        $this->assertSame([
            'id' => 'UTC', 'name' => 'UTC',
            'offset' => 0, 'offsetCode' => '+00:00',
        ], $info);
    }
}
