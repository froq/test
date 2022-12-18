<?php declare(strict_types=1);
namespace test\froq\reflection;
use froq\reflection\{ReflectionClassConstant, ReflectionClass, ReflectionType};
use froq\reflection\document\ClassConstantDocument;

class ReflectionClassConstantTest extends \TestCase
{
    function testGetterMethods() {
        $ref = new ReflectionClassConstant(\Locale::class, 'PATTERN');
        $this->assertSame(\Locale::class, $ref->getClass());
        $this->assertInstanceOf(ReflectionClass::class, $ref->getDeclaringClass());
        $this->assertSame('string', $ref->getType()->name);
        $this->assertSame('public', $ref->getVisibility());
        $this->assertSame(['public'], $ref->getModifierNames());
    }

    function testAttributeMethods() {
        $ref = new ReflectionClassConstant(\Locale::class, 'PATTERN');
        $this->assertInstanceOf(\Set::class, $ref->attributes());
        $this->assertCount(0, $ref->attributes());
        $this->assertFalse($ref->hasAttribute('Foo'));
        $this->assertNull($ref->getAttribute('Foo'));
        $this->assertSame([], $ref->getAttributeNames());
    }

    // @todo: 8.2 / Trait constants.
    // function testTraitMethods() {
    //     $ref = new ReflectionClassConstant(\Locale::class, 'PATTERN');
    //     $this->assertInstanceOf(\Set::class, $ref->traits());
    //     $this->assertCount(0, $ref->traits());
    //     $this->assertNull($ref->getTrait('Foo'));
    //     $this->assertSame([], $ref->getTraits());
    //     $this->assertSame([], $ref->getTraitNames());
    // }

    function testInterfaceMethods() {
        $ref = new ReflectionClassConstant(\Locale::class, 'PATTERN');
        $this->assertInstanceOf(\Set::class, $ref->interfaces());
        $this->assertCount(0, $ref->interfaces());
        $this->assertNull($ref->getInterface());
        $this->assertNull($ref->getInterface('Foo'));
        $this->assertSame([], $ref->getInterfaces());
        $this->assertNull($ref->getInterfaceName());
        $this->assertSame([], $ref->getInterfaceNames());
    }

    function testTypeMethods() {
        $ref = new ReflectionClassConstant(\Locale::class, 'PATTERN');
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
