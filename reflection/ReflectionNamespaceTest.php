<?php declare(strict_types=1);
namespace test\froq\reflection;
use froq\reflection\{ReflectionNamespace, ReflectionClass, ReflectionTrait};

class ReflectionNamespaceTest extends \TestCase
{
    function test_constructor() {
        $ref = new ReflectionNamespace('');
        $this->assertSame('', $ref->name);

        $ref = new ReflectionNamespace(__namespace__);
        $this->assertSame(__namespace__, $ref->name);

        $this->expectException(\ReflectionException::class);
        $this->expectExceptionMessage('Invalid namespace: " invalid "');
        new ReflectionNamespace(' invalid ');
    }

    function test_getters() {
        $ref = new ReflectionNamespace('');
        $this->assertSame('', $ref->getName());
        $this->assertSame('', $ref->getBasename());

        $ref = new ReflectionNamespace(__namespace__);
        $this->assertSame('test\froq\reflection', $ref->getName());
        $this->assertSame('test', $ref->getBasename());
    }

    function test_classMethods() {
        $ref = new ReflectionNamespace('froq\pager');
        $this->assertTrue($ref->hasClass('Pager'));
        $this->assertInstanceOf(ReflectionClass::class, $ref->getClass('Pager'));
        $this->assertEquals([new ReflectionClass('froq\pager\Pager')], $ref->getClasses());
        $this->assertEquals(['froq\pager\Pager'], $ref->getClassNames());
    }

    function test_interfaceMethods() {
        $ref = new ReflectionNamespace('froq\pager');
        $this->assertFalse($ref->hasInterface('PagerInterface'));
        $this->assertNull($ref->getInterface('PagerInterface'));
        $this->assertEquals([], $ref->getInterfaces());
        $this->assertEquals([], $ref->getInterfaceNames());
    }

    function test_traitMethods() {
        $ref = new ReflectionNamespace('froq\pager');
        $this->assertTrue($ref->hasTrait('PagerTrait'));
        $this->assertInstanceOf(ReflectionTrait::class, $ref->getTrait('PagerTrait'));
        $this->assertEquals([new ReflectionTrait('froq\pager\PagerTrait')], $ref->getTraits());
        $this->assertEquals(['froq\pager\PagerTrait'], $ref->getTraitNames());
    }
}
