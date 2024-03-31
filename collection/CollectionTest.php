<?php declare(strict_types=1);
namespace test\froq\collection;
use froq\collection\{Collection, CollectionException};

class CollectionTest extends \TestCase
{
    function testEmpty() {
        $col = new Collection();

        $this->assertTrue($col->isEmpty());
    }
    function testNotEmpty() {
        $col = new Collection(['x' => 123]);

        $this->assertTrue($col->isNotEmpty());
    }

    function testCount() {
        $col = new Collection(['x' => 123]);

        $this->assertSame(1, $col->count());
        $this->assertSame(1, count($col));
        $this->assertCount(1, $col);
    }

    function testAlterMethods() {
        $col = new Collection();

        $col->set('x', 123);
        $this->assertTrue($col->has('x'));
        $this->assertSame(123, $col->get('x'));

        $col->remove('x');
        $this->assertFalse($col->has('x'));

        $col->set('y', 456);
        $this->assertSame(456, $col->get('y', drop: true));

        $this->assertEmpty($col);
        $this->assertCount(0, $col);

        $col->push('foo');
        $this->assertSame('foo', $col->get(0));

        $foo = $col->pop();
        $this->assertSame('foo', $foo);

        $this->assertEquals(0, $col->empty()->count());
        $this->assertEquals(2, $col->append('1', '2')->count());
        $this->assertEquals(4, $col->prepend('3', '4')->count());

        // This methods are strict.
        $col->delete('1', 2, '3', '4');
        $this->assertCount(1, $col);
        $this->assertFalse($col->contains(2));
        $this->assertTrue($col->contains('2'));

        $col->empty();

        // Set multi items.
        $col->set(['x' => 1, 'y' => 2, 'z' => 3]);
        $this->assertTrue($col->contains(1));
        $this->assertTrue($col->containsKey('x'));

        $this->assertFalse($col->deleteKey('x')->containsKey('x'));
        $this->assertSame(['z' => 3, 'y' => 2], $col->reverse()->toArray());
    }

    function testAccessMethods() {
        $col = new Collection(['x' => 1, 'y' => 2, 'z' => '3', 'w' => null]);

        $this->assertEquals(1, $col['x']);
        $this->assertEquals(1, $col->x);

        $this->assertNull($col['foo']);
        $this->assertNull($col->foo);

        $this->assertIsString($col->get('z'));
        $this->assertIsInt($col->getInt('z'));
        $this->assertIsFloat($col->getFloat('z'));

        // Method has() uses isset().
        $this->assertFalse($col->has('w'));
        $this->assertTrue($col->hasKey('w'));
        $this->assertTrue($col->hasValue(null));
    }

    function testSearchMethods() {
        $col = new Collection(['x' => 1, 'y' => 2, 'z' => '3', 'w' => null, 2]);

        $this->assertEquals('y', $col->indexOf(2));
        $this->assertEquals(0, $col->lastIndexOf(2));

        $this->assertTrue($col->test(fn($v) => $v !== null));
        $this->assertFalse($col->testAll(fn($v) => $v !== null));

        $this->assertEquals(2, $col->find(fn($v) => $v > 1));
        $this->assertEquals('y', $col->findKey(fn($v) => $v > 1));
        $this->assertEquals(['y', 0], $col->findKeys(fn($v) => $v == 2));
        $this->assertEquals(['y' => 2, 0 => 2], $col->findAll(fn($v) => $v == 2));

        $this->assertEquals([1, 2], $col->select(['x', 'y'], combine: false)->toArray());
        $this->assertEquals(['x' => 1, 'y' => 2], $col->select(['x', 'y'], combine: true)->toArray());

        $this->assertEquals(1, $col->first());
        $this->assertEquals('x', $col->firstKey());
        $this->assertEquals(2, $col->last());
        $this->assertEquals(0, $col->lastKey());
    }

    function testCalculateMethods() {
        $col = new Collection(['x' => 1, 'y' => 2, 'z' => 3]);

        $this->assertEquals(1, $col->min());
        $this->assertEquals(3, $col->max());
        $this->assertEquals(6, $col->sum());
        $this->assertEquals(6, $col->product());
        $this->assertEquals(2.0, $col->average());
        $this->assertEquals($col->average(), $col->avg()); // Alias.
    }

    function testConvertMethods() {
        $array  = ['x' => 1, 'y' => 2, 'z' => 3];
        $object = (object) $array;
        $json   = json_encode($array);
        $list   = array_values($array);
        $serial = 'O:26:"froq\collection\Collection":3:{s:1:"x";i:1;s:1:"y";i:2;s:1:"z";i:3;}';

        $col = new Collection($array);

        $this->assertEquals($array, $col->toArray());
        $this->assertEquals($object, $col->toObject());
        $this->assertEquals($json, $col->toJson());
        $this->assertEquals($list, $col->toList());
        $this->assertEquals($serial, serialize($col));

        // JsonSerializable.
        $this->assertEquals($json, json_encode($col));
    }

    function testLoopMethods() {
        $col = new Collection(['x' => 1, 'y' => 2, 'z' => 3]);

        $sum = $col->copy()
            ->filter(fn($v) => $v > 1)
            ->map(fn($v) => $v + 1)
            ->reduce(0, fn($a, $v) => $a += $v);

        $applied = $col->copy()
            ->apply(fn($v) => $v ** 2);

        $aggregated = $col->copy()
            ->aggregate(fn(&$a, $v) => $a[] = $v + 1);

        $this->assertEquals(7, $sum);
        $this->assertEquals(14, $applied->sum());
        $this->assertEquals(9, $aggregated->sum());

        $this->assertEquals([1, 4, 9], $applied->toList());
        $this->assertEquals([2, 3, 4], $aggregated->toArray());
    }

    function testSortMethods() {
        $col = new Collection(['z' => 3, 'x' => 1, 'y' => 2]);
        $asc = [1, 2, 3]; $desc = [3, 2, 1];

        $this->assertEquals($asc, $col->sort()->values());
        $this->assertEquals($desc, $col->sort(-1)->values()); // Reverse.

        $this->assertEquals($asc, $col->sort(fn($a, $b) => $a - $b)->values());
        $this->assertEquals($desc, $col->sort(fn($a, $b) => $b - $a)->values()); // Reverse.

        $this->assertEquals(['x', 'y', 'z'], $col->sortKey()->keys());
    }

    function testUtilityMethods() {
        $col = new Collection($data = [1, 2, '2', 'foo' => null, 0]);

        $this->assertEquals([1, 2, '2', 0], $col->copy()->refine()->values());
        $this->assertEquals([1, 2, '2', null], $col->copy()->refine([0])->values());

        $this->assertEquals(array_values($data), $col->copy()->union([1, 2])->values()); // No dups.
        $this->assertEquals(array_values($data), $col->copy()->dedupe()->values()); // Strict.
        $this->assertEquals([1, 2, null, 0], $col->copy()->unique()->values()); // Not strict.
        $this->assertEquals([1, 2, null], $col->copy()->unique(SORT_REGULAR)->values()); // Not strict.
    }
}
