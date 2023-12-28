<?php declare(strict_types=1);
namespace test\froq\collection\iterator;
use froq\collection\iterator\ArrayIterator;

class ArrayIteratorTest extends \TestCase
{
    function testConstructor() {
        $it = new ArrayIterator();
        $this->assertSame([], $it->toArray());

        $it = new ArrayIterator([1, 2]);
        $this->assertSame([1, 2], $it->toArray());

        $it = new ArrayIterator(new \XArray([1, 2]));
        $this->assertSame([1, 2], $it->toArray());
    }

    function testSort() {
        $it = new ArrayIterator([1, 2, 0]);
        $it->sort();
        $this->assertSame([0, 1, 2], $it->toArray());

        $it = new ArrayIterator([1, 2, 0]);
        $it->sort(key: true);
        $this->assertSame([1, 2, 0], $it->toArray());
    }

    function testFilter() {
        $it = new ArrayIterator([1, 2, null]);
        $it->filter();
        $this->assertSame([1, 2], $it->toArray());

        $it = new ArrayIterator([1, 2, '0', null]);
        $it->filter(fn($v) => is_scalar($v));
        $this->assertSame([1, 2, '0'], $it->toArray());
    }

    function testMap() {
        $it = new ArrayIterator([1, 2, 0]);
        $it->map(fn($v) => $v * 2);
        $this->assertSame([2, 4, 0], $it->toArray());
    }

    function testReduce() {
        $it = new ArrayIterator([1, 2, 0]);
        $ret = $it->reduce(0, fn($r, $v) => $r += $v);
        $this->assertSame(3, $ret);
    }

    function testKeysValues() {
        $it = new ArrayIterator([1, 2, 0]);
        $this->assertSame([0, 1, 2], $it->keys());
        $this->assertSame([1, 2, 0], $it->values());
    }

    function testAppend() {
        $it = new ArrayIterator([0]);
        $it->append(1, 2);
        $this->assertSame([0, 1, 2], $it->toArray());
    }

    function testToArray() {
        $it = new ArrayIterator([0, 1]);
        $this->assertSame([0, 1], $it->toArray());
    }

    function testToList() {
        $it = new ArrayIterator(['a'=> 0, 'b' => 1]);
        $this->assertSame([0, 1], $it->toList());
    }

    function testJsonSerialize() {
        $it = new ArrayIterator(['a'=> 0, 'b' => 1]);
        $this->assertSame('{"a":0,"b":1}', json_encode($it));
    }
}
