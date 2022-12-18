<?php declare(strict_types=1);
namespace test\froq\collection\collector;
use froq\collection\collector\ListCollector;

class ListCollectorTest extends \TestCase
{
    function testAdd() {
        $col = new ListCollector();
        $this->assertSame([], $col->toArray());

        $col->add(1);
        $this->assertSame([1], $col->toArray());
    }

    function testSet() {
        $col = new ListCollector();
        $this->assertSame([], $col->toArray());

        $col->set(0, 0)->set(999, 1);
        $this->assertSame([0 => 0, 1 => 1], $col->toArray());

        $this->expectException(\TypeError::class);
        $col->set('a', 1);
    }

    function testGet() {
        $col = new ListCollector();
        $this->assertNull($col->get(0));

        $col->set(0, 1);
        $this->assertSame(1, $col->get(0));

        $this->expectException(\TypeError::class);
        $col->get('a', 1);
    }

    function testRemove() {
        $col = new ListCollector();
        $this->assertFalse($col->remove(0));

        $col->set(0, 1);
        $this->assertTrue($col->remove(0));

        $this->expectException(\TypeError::class);
        $col->remove('a');
    }

    function testRemoveValue() {
        $col = new ListCollector();
        $this->assertFalse($col->removeValue(1));

        $col->set(0, 1);
        $this->assertTrue($col->removeValue(1));
    }

    function testReplace() {
        $col = new ListCollector();
        $this->assertFalse($col->replace(0, 2));

        $col->set(0, 1);
        $this->assertTrue($col->replace(0, 2));

        $this->expectException(\TypeError::class);
        $col->replace('a', 2);
    }

    function testReplaceValue() {
        $col = new ListCollector();
        $this->assertFalse($col->replaceValue(1, 2));

        $col->set(0, 1);
        $this->assertTrue($col->replaceValue(1, 2));
    }

    function testHasMethods() {
        $col = new ListCollector();
        $this->assertFalse($col->has(0));
        $this->assertFalse($col->hasKey(0));
        $this->assertFalse($col->hasValue(1, $key));
        $this->assertNull($key);

        $col->set(0, 1);
        $this->assertTrue($col->has(0));
        $this->assertTrue($col->hasKey(0));
        $this->assertTrue($col->hasValue(1, $key));
        $this->assertSame(0, $key);
    }
}
