<?php declare(strict_types=1);
namespace test\froq\collection\iterator;
use froq\collection\iterator\ArrayIterator;

class ArrayIteratorTest extends \TestCase
{
    function test_constructor() {
        $it = new ArrayIterator();
        $this->assertSame([], $it->toArray());

        $it = new ArrayIterator([1, 2]);
        $this->assertSame([1, 2], $it->toArray());

        $it = new ArrayIterator(new \XArray([1, 2]));
        $this->assertSame([1, 2], $it->toArray());
    }

    function test_sort() {
        $it = new ArrayIterator([1, 2, 0]);
        $it->sort();
        $this->assertSame([0, 1, 2], $it->toArray());
    }

    function test_reverse() {
        $it = new ArrayIterator([1, 2, 0]);
        $it->reverse();
        $this->assertSame([0, 2, 1], $it->toArray());
    }

    function test_append() {
        $it = new ArrayIterator([0]);
        $it->append(1, 2);
        $this->assertSame([0, 1, 2], $it->toArray());
    }

    function test_toArray() {
        $it = new ArrayIterator([0, 1]);
        $this->assertSame([0, 1], $it->toArray());
    }

    function test_toList() {
        $it = new ArrayIterator(['a'=> 0, 'b' => 1]);
        $this->assertSame([0, 1], $it->toList());
    }

    function test_jsonSerialize() {
        $it = new ArrayIterator(['a'=> 0, 'b' => 1]);
        $this->assertSame('{"a":0,"b":1}', json_encode($it));
    }
}
