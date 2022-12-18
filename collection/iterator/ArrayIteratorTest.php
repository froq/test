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
    }

    function testSlice() {
        $it = new ArrayIterator([1, 2, 0]);
        $it->slice(1, 1);
        $this->assertSame([2], $it->toArray());
    }

    function testReverse() {
        $it = new ArrayIterator([1, 2, 0]);
        $it->reverse();
        $this->assertSame([0, 2, 1], $it->toArray());
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
