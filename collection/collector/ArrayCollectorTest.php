<?php declare(strict_types=1);
namespace test\froq\collection\collector;
use froq\collection\collector\ArrayCollector;

class ArrayCollectorTest extends \TestCase
{
    function test_add() {
        $col = new ArrayCollector();
        $this->assertSame([], $col->toArray());

        $col->add(1);
        $this->assertSame([1], $col->toArray());
    }

    function test_set() {
        $col = new ArrayCollector();
        $this->assertSame([], $col->toArray());

        $col->set(0, 0)->set('a', 1);
        $this->assertSame([0 => 0, 'a' => 1], $col->toArray());
    }

    function test_get() {
        $col = new ArrayCollector();
        $this->assertNull($col->get('a'));

        $col->set('a', 1);
        $this->assertSame(1, $col->get('a'));
    }

    function test_remove() {
        $col = new ArrayCollector();
        $this->assertFalse($col->remove('a'));

        $col->set('a', 1);
        $this->assertTrue($col->remove('a'));
    }

    function test_removeValue() {
        $col = new ArrayCollector();
        $this->assertFalse($col->removeValue(1));

        $col->set('a', 1);
        $this->assertTrue($col->removeValue(1));
    }

    function test_replace() {
        $col = new ArrayCollector();
        $this->assertFalse($col->replace('a', 2));

        $col->set('a', 1);
        $this->assertTrue($col->replace('a', 2));
    }

    function test_replaceValue() {
        $col = new ArrayCollector();
        $this->assertFalse($col->replaceValue(1, 2));

        $col->set('a', 1);
        $this->assertTrue($col->replaceValue(1, 2));
    }

    function test_hasMethods() {
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
