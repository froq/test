<?php declare(strict_types=1);
namespace froq\test\collection;
use froq\collection\{ItemCollection, CollectionException};
use froq\common\exception\InvalidKeyException;

class ItemCollectionTest extends \PHPUnit\Framework\TestCase
{
    function test_invalidKeyException() {
        try {
            new ItemCollection(['x' => 1]);
        } catch (\Throwable $e) {
            $this->assertInstanceOf(InvalidKeyException::class, $e);
            $this->assertStringContainsString('keys must be int', $e->getMessage());
        }
    }

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
