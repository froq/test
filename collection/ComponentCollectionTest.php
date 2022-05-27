<?php
namespace froq\collection\test;
use froq\collection\{ComponentCollection, CollectionException};

class ComponentCollectionTest extends \PHPUnit\Framework\TestCase
{
    function test_emptyNamesException() {
        try {
            new ComponentCollection([]);
        } catch (\Throwable $e) {
            $this->assertInstanceOf(CollectionException::class, $e);
            $this->assertStringContainsString('names', $e->getMessage());
        }
    }

    function test_invalidNameException() {
        $this->expectException(CollectionException::class);

        $col = new ComponentCollection(['x', 'y']);
        $col->get('foo');
    }

    function test_namesAndHasMethods() {
        $col = new ComponentCollection(['x', 'y']);

        $this->assertEquals(['x', 'y'], $col->names());
        $this->assertFalse($col->has('x'));
        $this->assertTrue($col->hasName('x'));
        $this->assertFalse($col->hasValue('x'));
    }

    function test_accessAndAlterMethods() {
        $col = new ComponentCollection(['x', 'y']);
        $col->set('x', 1)->set('y', null);

        $this->assertTrue($col->has('x'));
        $this->assertFalse($col->has('y'));

        $this->assertEquals($col->get('x'), 1);
        $this->assertEquals($col->get('y'), null);

        $this->assertEquals($col->x, 1);
        $this->assertEquals($col->y, null);

        $this->assertEquals($col['x'], 1);
        $this->assertEquals($col['y'], null);

        $col->remove('x');
        $this->assertFalse($col->has('x'));
        $this->assertTrue($col->hasName('x'));

        $col->set('x', '123');
        $this->assertTrue($col->get('x') === '123');
        $this->assertTrue($col->getInt('x') === 123);
    }

    function test_callMagicMethod() {
        $col = new ComponentCollection(['id', 'name']);
        $col->setId(1)->setName('Foo!');

        $this->assertEquals(1, $col->getId());
        $this->assertEquals('Foo!', $col->getName());

        try {
            $col->setAge(20);
        } catch (\Throwable $e) {
            $this->assertInstanceOf(CollectionException::class, $e);
            $this->assertStringContainsString('Invalid component name', $e->getMessage());
        }

        try {
            $col->undefinedMethod();
        } catch (\Throwable $e) {
            $this->assertInstanceOf(CollectionException::class, $e);
            $this->assertStringContainsString('Invalid method call', $e->getMessage());
        }
    }
}
