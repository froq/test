<?php declare(strict_types=1);
namespace test\froq\collection\iterator;
use froq\collection\iterator\ReverseArrayIterator;

class ReverseArrayIteratorTest extends \TestCase
{
    function testConstructor() {
        $it = new ReverseArrayIterator();
        $this->assertSame([], $it->toArray());

        $it = new ReverseArrayIterator([1, 2]);
        $this->assertSame([2, 1], $it->toArray());

        $it = new ReverseArrayIterator(new \XArray([1, 2]));
        $this->assertSame([2, 1], $it->toArray());
    }
}
