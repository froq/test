<?php declare(strict_types=1);
namespace test\froq\reflection;
use froq\reflection\{ReflectionProperty, ReflectionClass, ReflectionNamespace};
use froq\reflection\document\PropertyDocument;

class ReflectionPropertyTest extends \TestCase
{
    function testGetterMethods() {
        $ref = new ReflectionProperty(\RegExp::class, 'source');
        $this->assertSame('RegExp$source', $ref->getLongName());
        $this->assertSame('', $ref->getNamespace());
        $this->assertInstanceOf(ReflectionNamespace::class, $ref->getDeclaringNamespace());
        $this->assertSame(\RegExp::class, $ref->getClass());
        $this->assertInstanceOf(ReflectionClass::class, $ref->getDeclaringClass());
        $this->assertSame('string', $ref->getType()->name);
        $this->assertSame('public', $ref->getVisibility());
        $this->assertSame(['public', 'readonly'], $ref->getModifierNames());
    }

    function testCheckerMethods() {
        $ref = new ReflectionProperty(\RegExp::class, 'source');
        $this->assertFalse($ref->isDynamic());
        $this->assertFalse($ref->isInitialized());
        $this->assertFalse($ref->isNullable());
        $this->assertFalse($ref->allowsNull()); // Alias.

        $class = new class('tr') extends \RegExp {};

        $ref = new ReflectionProperty($class, 'source');
        $this->assertTrue($ref->isInitialized());

        $class = new \stdClass();
        $class->x = null;

        $ref = new ReflectionProperty($class, 'x');
        $this->assertTrue($ref->isDynamic());
        $this->assertTrue($ref->isInitialized());
        $this->assertTrue($ref->isNullable());
        $this->assertTrue($ref->allowsNull()); // Alias.
    }

    function testAttributeMethods() {
        $ref = new ReflectionProperty(\RegExp::class, 'source');
        $this->assertInstanceOf(\Set::class, $ref->attributes());
        $this->assertCount(0, $ref->attributes());
        $this->assertFalse($ref->hasAttribute('Foo'));
        $this->assertNull($ref->getAttribute('Foo'));
        $this->assertSame([], $ref->getAttributeNames());
    }

    function testTraitMethods() {
        $ref = new ReflectionProperty(\RegExp::class, 'source');
        $this->assertInstanceOf(\Set::class, $ref->traits());
        $this->assertCount(0, $ref->traits());
        $this->assertNull($ref->getTrait('Foo'));
        $this->assertSame([], $ref->getTraits());
        $this->assertSame([], $ref->getTraitNames());
    }

    function testValueMethods() {
        $class = new class('tr') extends \RegExp {};

        $ref = new ReflectionProperty($class, 'source');
        $this->assertSame('tr', $ref->getValue());

        $class = new \stdClass();
        $class->x = null;

        $ref = new ReflectionProperty($class, 'x');
        $ref->setValue(123);
        $this->assertSame(123, $ref->getValue());
        $ref->setValue($class, 456);
        $this->assertSame(456, $ref->getValue($class));

        try {
            $ref = new ReflectionProperty(\RegExp::class, 'source');
            $ref->setValue('en');
        } catch (\ReflectionException $e) {
            $this->assertSame('Cannot set property $source of non-instantiated class RegExp',
                $e->getMessage());
        }

        try {
            $ref = new ReflectionProperty(\RegExp::class, 'source');
            $ref->getValue();
        } catch (\ReflectionException $e) {
            $this->assertSame('Cannot get property $source of non-instantiated class RegExp',
                $e->getMessage());
        }
    }

    function testDocumentMethods() {
        require_once __DIR__ . '/../.etc/util/reflections.php';

        $ref = new ReflectionProperty('foo\bar\Test::a');
        $this->assertSame('The var.', $ref->getDocumentDescription());

        $doc = $ref->getDocument();
        $this->assertInstanceOf(PropertyDocument::class, $doc);
        $this->assertSame('The var.', $doc->getDescription());
        $this->assertSame('bool', $doc->getVariable()->getType());
        $this->assertNull($doc->getVariable()->getName());
    }
}
