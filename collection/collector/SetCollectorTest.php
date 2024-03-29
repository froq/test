<?php declare(strict_types=1);
namespace test\froq\collection\collector;
use froq\collection\collector\SetCollector;

class SetCollectorTest extends \TestCase
{
    function testConstructor() {
        $col = new SetCollector([1, 2, 2]);
        $this->assertSame([1, 2], $col->toArray());

        $col = new SetCollector(new \Set([1, 2, 2]));
        $this->assertSame([1, 2], $col->toArray());
    }

    function testAdd() {
        $col = new SetCollector();
        $this->assertSame([], $col->toArray());

        $col->add(1);
        $this->assertSame([1], $col->toArray());
    }

    function testSet() {
        $col = new SetCollector();
        $this->assertSame([], $col->toArray());

        $col->set(0, 0)->set(999, 1);
        $this->assertSame([0 => 0, 1 => 1], $col->toArray());

        $this->expectException(\TypeError::class);
        $col->set('a', 1);
    }

    function testGet() {
        $col = new SetCollector();
        $this->assertNull($col->get(0));

        $col->set(0, 1);
        $this->assertSame(1, $col->get(0));

        $this->expectException(\TypeError::class);
        $col->get('a', 1);
    }

    function testRemove() {
        $col = new SetCollector();
        $this->assertFalse($col->remove(0));

        $col->set(0, 1);
        $this->assertTrue($col->remove(0));

        $this->expectException(\TypeError::class);
        $col->remove('a');
    }

    function testRemoveValue() {
        $col = new SetCollector();
        $this->assertFalse($col->removeValue(1));

        $col->set(0, 1);
        $this->assertTrue($col->removeValue(1));
    }

    function testReplace() {
        $col = new SetCollector();
        $this->assertFalse($col->replace(0, 2));

        $col->set(0, 1);
        $this->assertTrue($col->replace(0, 2));

        $this->expectException(\TypeError::class);
        $col->replace('a', 2);
    }

    function testReplaceValue() {
        $col = new SetCollector();
        $this->assertFalse($col->replaceValue(1, 2));

        $col->set(0, 1);
        $this->assertTrue($col->replaceValue(1, 2));
    }

    function testHasMethods() {
        $col = new SetCollector();
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
