<?php declare(strict_types=1);
namespace test\froq\collection\collector;
use froq\collection\collector\ArrayCollector;

class ArrayCollectorTest extends \TestCase
{
    function testAdd() {
        $col = new ArrayCollector();
        $this->assertSame([], $col->toArray());

        $col->add(1);
        $this->assertSame([1], $col->toArray());
    }

    function testSet() {
        $col = new ArrayCollector();
        $this->assertSame([], $col->toArray());

        $col->set(0, 0)->set('a', 1);
        $this->assertSame([0 => 0, 'a' => 1], $col->toArray());
    }

    function testGet() {
        $col = new ArrayCollector();
        $this->assertNull($col->get('a'));

        $col->set('a', 1);
        $this->assertSame(1, $col->get('a'));
    }

    function testRemove() {
        $col = new ArrayCollector();
        $this->assertFalse($col->remove('a'));

        $col->set('a', 1);
        $this->assertTrue($col->remove('a'));
    }

    function testRemoveValue() {
        $col = new ArrayCollector();
        $this->assertFalse($col->removeValue(1));

        $col->set('a', 1);
        $this->assertTrue($col->removeValue(1));
    }

    function testReplace() {
        $col = new ArrayCollector();
        $this->assertFalse($col->replace('a', 2));

        $col->set('a', 1);
        $this->assertTrue($col->replace('a', 2));
    }

    function testReplaceValue() {
        $col = new ArrayCollector();
        $this->assertFalse($col->replaceValue(1, 2));

        $col->set('a', 1);
        $this->assertTrue($col->replaceValue(1, 2));
    }

    function testHasMethods() {
        $col = new ArrayCollector();
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
