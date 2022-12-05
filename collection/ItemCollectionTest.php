<?php declare(strict_types=1);
namespace test\froq\collection;
use froq\collection\{ItemCollection, CollectionException};

class ItemCollectionTest extends \TestCase
{
    function test_accessAndAlterMethods() {
        $col = new ItemCollection([null]);

        $this->assertFalse($col->has(0));
        $this->assertTrue($col->hasIndex(0));
        $this->assertFalse($col->hasItem('foo'));

        $col->set(0, 'foo');
        $this->assertTrue($col->has(0));
        $this->assertTrue($col->hasIndex(0));
        $this->assertTrue($col->hasItem('foo'));

        $col->add('bar');
        $this->assertTrue($col->has(1));
        $this->assertTrue($col->hasIndex(1));
        $this->assertTrue($col->hasItem('bar'));

        $this->assertTrue($col->replace('bar', 'baz'));
        $this->assertFalse($col->replace('bat', 'baz'));
        $this->assertEquals('baz', $col->item(1));
        $this->assertEquals(['foo', 'baz'], $col->items());

        $col[] = 'last item';
        $this->assertEquals('last item', $col->last());
        $this->assertEquals('foo', $col->first());
    }
}
