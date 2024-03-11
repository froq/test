<?php declare(strict_types=1);
namespace test\froq\reflection;
use froq\reflection\{ReflectionClassConstant, ReflectionClass, ReflectionNamespace, ReflectionType};
use froq\reflection\document\ClassConstantDocument;

class ReflectionClassConstantTest extends \TestCase
{
    function testGetterMethods() {
        $ref = new ReflectionClassConstant(\RegExp::class, 'DELIMITER');
        $this->assertSame('RegExp@DELIMITER', $ref->getLongName());
        $this->assertSame('', $ref->getNamespace());
        $this->assertInstanceOf(ReflectionNamespace::class, $ref->getDeclaringNamespace());
        $this->assertSame(\RegExp::class, $ref->getClass());
        $this->assertInstanceOf(ReflectionClass::class, $ref->getDeclaringClass());
        $this->assertSame('string', $ref->getType()->name);
        $this->assertSame('public', $ref->getVisibility());
        $this->assertSame(['final', 'public'], $ref->getModifierNames());
    }

    function testAttributeMethods() {
        $ref = new ReflectionClassConstant(\RegExp::class, 'DELIMITER');
        $this->assertInstanceOf(\Set::class, $ref->attributes());
        $this->assertCount(0, $ref->attributes());
        $this->assertFalse($ref->hasAttribute('Foo'));
        $this->assertNull($ref->getAttribute('Foo'));
        $this->assertSame([], $ref->getAttributeNames());
    }

    function testTraitMethods() {
        $ref = new ReflectionClassConstant(\RegExp::class, 'DELIMITER');
        $this->assertInstanceOf(\Set::class, $ref->traits());
        $this->assertCount(0, $ref->traits());
        $this->assertNull($ref->getTrait('Foo'));
        $this->assertSame([], $ref->getTraits());
        $this->assertSame([], $ref->getTraitNames());
    }

    function testInterfaceMethods() {
        $ref = new ReflectionClassConstant(\RegExp::class, 'DELIMITER');
        $this->assertInstanceOf(\Set::class, $ref->interfaces());
        $this->assertCount(0, $ref->interfaces());
        $this->assertNull($ref->getInterface());
        $this->assertNull($ref->getInterface('Foo'));
        $this->assertSame([], $ref->getInterfaces());
        $this->assertNull($ref->getInterfaceName());
        $this->assertSame([], $ref->getInterfaceNames());
    }

    function testTypeMethods() {
        $ref = new ReflectionClassConstant(\RegExp::class, 'DELIMITER');
        $this->assertInstanceOf(ReflectionType::class, $ref->getType());
        $this->assertSame('string', $ref->getType()->getName());
        $this->assertEquals([new ReflectionType('string')], $ref->getTypes());
    }

    function testDocumentMethods() {
        require_once __DIR__ . '/../.etc/util/reflections.php';

        $ref = new ReflectionClassConstant('foo\bar\Test::A');
        $this->assertSame('The const.', $ref->getDocumentDescription());

        $doc = $ref->getDocument();
        $this->assertInstanceOf(ClassConstantDocument::class, $doc);
        $this->assertSame('The const.', $doc->getDescription());
        $this->assertSame('int', $doc->getConstant()->getType());
        $this->assertNull($doc->getConstant()->getName());
    }
}
