<?php declare(strict_types=1);
namespace test\froq\reflection;
use froq\reflection\{ReflectionNamespace, ReflectionClass, ReflectionTrait};

class ReflectionNamespaceTest extends \TestCase
{
    function testConstructor() {
        $ref = new ReflectionNamespace('');
        $this->assertSame('', $ref->name);

        $ref = new ReflectionNamespace(__namespace__);
        $this->assertSame(__namespace__, $ref->name);

        $this->expectException(\ReflectionException::class);
        $this->expectExceptionMessage('Invalid namespace: " invalid "');
        new ReflectionNamespace(' invalid ');
    }

    function testGetters() {
        $ref = new ReflectionNamespace('');
        $this->assertSame('', $ref->getName());
        $this->assertSame('', $ref->getBasename());

        $ref = new ReflectionNamespace(__namespace__);
        $this->assertSame('test\froq\reflection', $ref->getName());
        $this->assertSame('test', $ref->getBasename());
    }

    function testClassMethods() {
        $ref = new ReflectionNamespace('froq\dom');
        $this->assertTrue($ref->hasClass('DomDocument'));
        $this->assertInstanceOf(ReflectionClass::class, $ref->getClass('DomDocument'));
        $this->assertEquals([new ReflectionClass('froq\dom\DomDocument')], slice($ref->getClasses(), 0, 1));
        $this->assertEquals(['froq\dom\DomDocument'], slice($ref->getClassNames(), 0, 1));
    }

    function testInterfaceMethods() {
        $ref = new ReflectionNamespace('froq\dom');
        $this->assertFalse($ref->hasInterface('DomDocumentInterface'));
        $this->assertNull($ref->getInterface('DomDocumentInterface'));
        $this->assertEquals([], $ref->getInterfaces());
        $this->assertEquals([], $ref->getInterfaceNames());
    }

    function testTraitMethods() {
        $ref = new ReflectionNamespace('froq\dom');
        $this->assertTrue($ref->hasTrait('NodeTrait'));
        $this->assertInstanceOf(ReflectionTrait::class, $ref->getTrait('NodeTrait'));
        $this->assertEquals([new ReflectionTrait('froq\dom\NodeTrait')], slice($ref->getTraits(), 0, 1));
        $this->assertEquals(['froq\dom\NodeTrait'], slice($ref->getTraitNames(), 0, 1));
    }
}
