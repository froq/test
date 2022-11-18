<?php declare(strict_types=1);
namespace test\froq\collection;
use froq\collection\{ComponentCollection, CollectionException};

class ComponentCollectionTest extends \TestCase
{
    function test_emptyNamesException() {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage('No names given');

        new ComponentCollection([]);
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

        $this->assertEquals(1, $col->get('x'));
        $this->assertEquals(null, $col->get('y'));

        $this->assertEquals(1, @$col->x);
        $this->assertEquals(null, @$col->y);

        $this->assertEquals(1, @$col['x']);
        $this->assertEquals(null, @$col['y']);

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
        } catch (CollectionException $e) {
            $this->assertStringContains('Invalid component name', $e->getMessage());
        }

        try {
            $col->undefinedMethod();
        } catch (CollectionException $e) {
            $this->assertStringContains('Invalid method call', $e->getMessage());
        }
    }
}
