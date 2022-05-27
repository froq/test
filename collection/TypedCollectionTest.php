<?php
namespace froq\test\collection;
use froq\collection\{TypedCollection, CollectionException};

class TypedCollectionTest extends \PHPUnit\Framework\TestCase
{
    function test_constructMethod() {
        try {
            new TypedCollection();
        } catch (\Throwable $e) {
            $this->assertInstanceOf(CollectionException::class, $e);
            $this->assertStringContainsString('Data type is required', $e->getMessage());
        }

        try {
            new TypedCollection(['1'], dataType: 'int');
        } catch (\Throwable $e) {
            $this->assertInstanceOf(CollectionException::class, $e);
            $this->assertStringContainsString('must be type of int', $e->getMessage());
        }
    }

    function test_alterMethodsWithTypeChecks() {
        $col = new TypedCollection([], 'int');

        $col->set('x', 1);
        $this->assertTrue($col->get('x') === 1);
        $this->assertTrue($col->get('y') === null);

        try {
            $col->set('y', '1');
        } catch (\Throwable $e) {
            $this->assertInstanceOf(CollectionException::class, $e);
            $this->assertStringContainsString('must be type of int, string given', $e->getMessage());
        }
    }
}
