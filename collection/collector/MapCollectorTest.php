<?php declare(strict_types=1);
namespace test\froq\collection\collector;
use froq\collection\collector\MapCollector;

class MapCollectorTest extends \TestCase
{
    function test_constructor() {
        $col = new MapCollector([1, 2]);
        $this->assertSame([1, 2], $col->toArray());

        $col = new MapCollector(new \Map([1, 2]));
        $this->assertSame([1, 2], $col->toArray());
    }

    function test_set() {
        $col = new MapCollector();
        $this->assertSame([], $col->toArray());

        $col->set('a', 1);
        $this->assertSame(['a' => 1], $col->toArray());

        $this->expectException(\TypeError::class);
        $col->set(0, 1);
    }

    function test_get() {
        $col = new MapCollector();
        $this->assertNull($col->get('a'));

        $col->set('a', 1);
        $this->assertSame(1, $col->get('a'));

        $this->expectException(\TypeError::class);
        $col->get(0, 1);
    }

    function test_remove() {
        $col = new MapCollector();
        $this->assertFalse($col->remove('a'));

        $col->set('a', 1);
        $this->assertTrue($col->remove('a'));

        $this->expectException(\TypeError::class);
        $col->remove(0);
    }

    function test_removeValue() {
        $col = new MapCollector();
        $this->assertFalse($col->removeValue(1));

        $col->set('a', 1);
        $this->assertTrue($col->removeValue(1));
    }

    function test_replace() {
        $col = new MapCollector();
        $this->assertFalse($col->replace('a', 2));

        $col->set('a', 1);
        $this->assertTrue($col->replace('a', 2));

        $this->expectException(\TypeError::class);
        $col->replace(1, 2);
    }

    function test_replaceValue() {
        $col = new MapCollector();
        $this->assertFalse($col->replaceValue(1, 2));

        $col->set('a', 1);
        $this->assertTrue($col->replaceValue(1, 2));
    }

    function test_hasMethods() {
        $col = new MapCollector();
        $this->assertFalse($col->has('a'));
        $this->assertFalse($col->hasKey('a'));
        $this->assertFalse($col->hasValue(1, $key));
        $this->assertNull($key);

        $col->set('a', 1);
        $this->assertTrue($col->has('a'));
        $this->assertTrue($col->hasKey('a'));
        $this->assertTrue($col->hasValue(1, $key));
        $this->assertSame('a', $key);
    }
}
