<?php declare(strict_types=1);
namespace test\froq\reflection;
use froq\reflection\{ReflectionClass, ReflectionClassConstant, ReflectionProperty,
    ReflectionMethod, ReflectionInterface};
use froq\util\Objects;

class ReflectionClassTest extends \TestCase
{
    function test_getters() {
        $ref = new ReflectionClass(self::class);
        $this->assertTrue($ref->isClass());
        $this->assertSame('class', $ref->getType());
        $this->assertSame(__class__, $ref->getName());
        $this->assertSame(__namespace__, $ref->getNamespace());
        $this->assertSame(__namespace__, $ref->getDeclaringNamespace()->name);
        $this->assertSame([], $ref->getModifierNames());

        $ref = new ReflectionClass(\Throwable::class);
        $this->assertFalse($ref->isClass());
        $this->assertSame('interface', $ref->getType());
        $this->assertSame('Throwable', $ref->getName());
        $this->assertSame('', $ref->getNamespace());
        $this->assertSame('', $ref->getDeclaringNamespace()->name);
        $this->assertSame([], $ref->getModifierNames());
    }

    function test_parentMethods() {
        $parents = [
            new ReflectionClass('TestCase'),
            new ReflectionClass('PHPUnit\Framework\TestCase'),
            new ReflectionClass('PHPUnit\Framework\Assert'),
        ];

        $ref = new ReflectionClass(self::class);
        $this->assertInstanceOf(\Set::class, $ref->parents());
        $this->assertCount(3, $ref->parents());
        $this->assertTrue($ref->hasParent());
        $this->assertEquals($parents[0], $ref->getParent());
        $this->assertEquals($parents[2], $ref->getParent(baseOnly: true));
        $this->assertEquals($parents, $ref->getParents());
        $this->assertSame(['TestCase', 'PHPUnit\Framework\TestCase', 'PHPUnit\Framework\Assert'],
            $ref->getParentNames());
        $this->assertSame('TestCase', $ref->getParentName());
        $this->assertSame('PHPUnit\Framework\Assert', $ref->getParentName(baseOnly: true));
        $this->assertEquals($parents[0], $ref->getParentClass());
        $this->assertEquals($parents[2], $ref->getParentClass(baseOnly: true));
        $this->assertEquals($parents, $ref->getParentClasses());
    }

    function test_attributeMethods() {
        $ref = new ReflectionClass($this);
        $this->assertInstanceOf(\Set::class, $ref->attributes());
        $this->assertCount(0, $ref->attributes());
        $this->assertFalse($ref->hasAttribute('Foo'));
        $this->assertNull($ref->getAttribute('Foo'));
        $this->assertSame([], $ref->getAttributeNames());
    }

    function test_interfaceMethods() {
        $interfaces = map($interfaceNames = Objects::getInterfaces($this),
            fn($interfaceName) => new ReflectionInterface($interfaceName));

        $ref = new ReflectionClass($this);
        $this->assertInstanceOf(\Set::class, $ref->interfaces());
        $this->assertCount(4, $ref->interfaces());
        $this->assertNotNull($ref->getInterface('Countable'));
        $this->assertNull($ref->getInterface('Foo'));
        $this->assertEquals($interfaces, $ref->getInterfaces());
        $this->assertSame($interfaceNames, $ref->getInterfaceNames());
    }

    function test_traitMethods() {
        $ref = new ReflectionClass($this);
        $this->assertInstanceOf(\Set::class, $ref->traits());
        $this->assertCount(0, $ref->traits());
        $this->assertNull($ref->getTrait('Foo'));
        $this->assertSame([], $ref->getTraits());
        $this->assertSame([], $ref->getTraitNames());
    }

    function test_methodMethods() {
        $class = new class('tr') extends \Locale {}; // Simpler, for own check.
        $methods = map($methodNames = Objects::getMethodNames($class),
            fn($methodName) => new ReflectionMethod($class, $methodName));

        $ref = new ReflectionClass($class);
        // prd($ref->getMethods(),1);
        $this->assertInstanceOf(\Set::class, $ref->methods());
        $this->assertCount(count($methods), $ref->methods());
        $this->assertNotNull($ref->getMethod('from'));
        $this->assertNull($ref->getMethod('foo'));
        // Doesn't work, PHPUnit converts anons to binary string: 0x4c6f63616c654...1243438
        // $this->assertEquals($methods, $ref->getMethods());
        $this->assertEquals($methodNames, $ref->getMethodNames());
    }

    function test_constantMethods() {
        $class = new class('tr') extends \Locale {}; // Simpler, for own check.
        $constants = map($constantNames = Objects::getConstantNames($class),
            fn($constantName) => new ReflectionClassConstant($class, $constantName));

        $ref = new ReflectionClass($class);
        $this->assertInstanceOf(\Set::class, $ref->constants());
        $this->assertCount(1, $ref->constants());
        $this->assertTrue($ref->hasConstant('PATTERN'));
        $this->assertFalse($ref->hasOwnConstant('PATTERN')); // Not own.
        $this->assertNotNull($ref->getConstant('PATTERN'));
        $this->assertNull($ref->getConstant('FOO'));
        $this->assertEquals($constants, $ref->getConstants());
        $this->assertEquals($constantNames, $ref->getConstantNames());
        $this->assertSame([\Locale::PATTERN], $ref->getConstantValues());
        $this->assertEquals($constants[0], $ref->getReflectionConstant('PATTERN'));
        $this->assertEquals($constants, $ref->getReflectionConstants());
    }

    function test_propertyMethods() {
        $class = new class('tr') extends \Locale {}; // Simpler, for own check.
        $properties = map($propertyNames = Objects::getPropertyNames($class),
            fn($propertyName) => new ReflectionProperty($class, $propertyName));

        $ref = new ReflectionClass($class);
        // prd($ref->getPropertyValues(),1);
        $this->assertInstanceOf(\Set::class, $ref->properties());
        $this->assertCount(5, $ref->properties());
        $this->assertTrue($ref->hasProperty('language'));
        $this->assertFalse($ref->hasOwnProperty('language')); // Not own.
        $this->assertNotNull($ref->getProperty('language'));
        $this->assertNull($ref->getProperty('foo')); // No exception.
        $this->assertEquals($properties, $ref->getProperties());
        $this->assertSame($propertyNames, $ref->getPropertyNames());
        $this->assertSame(['tr', null, null, null, null], $ref->getPropertyValues());

        $class = new class() extends \stdClass {};
        $class->x = 1;

        $ref = new ReflectionClass($class);
        $this->assertInstanceOf(\Set::class, $ref->properties());
        $this->assertCount(1, $ref->properties());
        $this->assertTrue($ref->hasProperty('x')); // Dynamic.
        $this->assertFalse($ref->hasProperty('y'));
        $this->assertNotNull($ref->getProperty('x'));
        $this->assertNull($ref->getProperty('y')); // No exception.
        $this->assertEquals([new ReflectionProperty($class, 'x')], $ref->getProperties());
        $this->assertSame(['x'], $ref->getPropertyNames());
        $this->assertSame([1], $ref->getPropertyValues());
    }
}
