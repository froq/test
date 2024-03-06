<?php declare(strict_types=1);
namespace test\froq\reflection;
use froq\reflection\ReflectionType;

class ReflectionTypeTest extends \TestCase
{
    function testConstructor() {
        new ReflectionType('int'); // OK.

        $this->expectException(\ReflectionException::class);
        $this->expectExceptionMessage('Invalid name');
        new ReflectionType('');
    }

    function testMagicGet() {
        $ref = new ReflectionType('int');
        $this->assertSame('int', $ref->name);
        $this->assertFalse($ref->nullable);

        $ref = new ReflectionType('?int');
        $this->assertSame('int|null', $ref->name);
        $this->assertTrue($ref->nullable);

        $ref = new ReflectionType('int|null');
        $this->assertSame('int|null', $ref->name);
        $this->assertTrue($ref->nullable);

        $this->expectException(\ReflectionException::class);
        $this->expectExceptionMessage('Undefined property froq\reflection\ReflectionType::$foo');
        $ref->foo;
    }

    function testMagicString() {
        $ref = new ReflectionType('?int');
        $this->assertIsString((string) $ref);
        $this->assertStringContains('int|null', (string) $ref);
    }

    function testGetterMethods() {
        $ref = new ReflectionType('int');
        $this->assertSame('int', $ref->getName());
        $this->assertSame('int', $ref->getPureName());
        $this->assertSame(['int'], $ref->getNames());
        $this->assertEquals([new ReflectionType('int')],
            $ref->getTypes());

        $ref = new ReflectionType('?int');
        $this->assertSame('int|null', $ref->getName());
        $this->assertSame('int', $ref->getPureName());
        $this->assertSame(['int', 'null'], $ref->getNames());
        $this->assertEquals([new ReflectionType('int'), new ReflectionType('null')],
            $ref->getTypes());

        $ref = new ReflectionType('int|null');
        $this->assertSame('int|null', $ref->getName());
        $this->assertSame('int', $ref->getPureName());
        $this->assertSame(['int', 'null'], $ref->getNames());
        $this->assertEquals([new ReflectionType('int'), new ReflectionType('null')],
            $ref->getTypes());

        $ref = new ReflectionType('int|float|null');
        $this->assertSame('int|float|null', $ref->getName());
        $this->assertNull($ref->getPureName());
        $this->assertSame(['int', 'float', 'null'], $ref->getNames());
        $this->assertEquals([new ReflectionType('int'), new ReflectionType('float'), new ReflectionType('null')],
            $ref->getTypes());
    }

    function testCheckerMethods() {
        $ref = new ReflectionType('int');
        $this->assertTrue($ref->isNamed());
        $this->assertFalse($ref->isUnion());
        $this->assertFalse($ref->isIntersection());
        $this->assertTrue($ref->isSingle());
        $this->assertFalse($ref->isMulti());
        // $this->assertFalse($ref->isMixed());
        $this->assertTrue($ref->isBuiltin());
        $this->assertTrue($ref->isPrimitive());
        $this->assertTrue($ref->isCastable());
        $this->assertFalse($ref->isNullable());
        $this->assertFalse($ref->allowsNull());

        $this->assertTrue($ref->equals('int'));
        $this->assertTrue($ref->contains('int'));

        $this->assertSame(1, $ref->count());
        $this->assertNull($ref->toReflectionClass());
        $this->assertEmpty($ref->toReflectionClasses());

        $ref = new ReflectionType('?int');
        $this->assertTrue($ref->isNamed());
        $this->assertFalse($ref->isUnion());
        $this->assertFalse($ref->isIntersection());
        $this->assertFalse($ref->isSingle());
        $this->assertTrue($ref->isMulti());
        // $this->assertFalse($ref->isMixed());
        $this->assertTrue($ref->isBuiltin());
        $this->assertFalse($ref->isPrimitive());
        $this->assertFalse($ref->isCastable());
        $this->assertTrue($ref->isNullable());
        $this->assertTrue($ref->allowsNull());

        $this->assertFalse($ref->equals('null'));
        $this->assertTrue($ref->contains('null'));

        $this->assertSame(2, $ref->count());
        $this->assertNull($ref->toReflectionClass());
        $this->assertEmpty($ref->toReflectionClasses());

        $ref = new ReflectionType('int|float|null');
        $this->assertFalse($ref->isNamed());
        $this->assertTrue($ref->isUnion());
        $this->assertFalse($ref->isIntersection());
        $this->assertFalse($ref->isSingle());
        $this->assertTrue($ref->isMulti());
        // $this->assertFalse($ref->isMixed());
        $this->assertFalse($ref->isBuiltin());
        $this->assertFalse($ref->isPrimitive());
        $this->assertFalse($ref->isCastable());
        $this->assertTrue($ref->isNullable());
        $this->assertTrue($ref->allowsNull());

        $this->assertFalse($ref->equals('null'));
        $this->assertTrue($ref->contains('null'));

        $this->assertSame(3, $ref->count());
        $this->assertNull($ref->toReflectionClass());
        $this->assertEmpty($ref->toReflectionClasses());

        $ref = new ReflectionType('mixed');
        $this->assertTrue($ref->isMixed());

        $ref = new ReflectionType('Error');
        $this->assertTrue($ref->isClass());
        $this->assertTrue($ref->isClassOf('Throwable'));
        $this->assertTrue($ref->isSubclassOf('Throwable'));

        $this->assertNotNull($ref->toReflectionClass());
        $this->assertNotNull($ref->toReflectionClasses());
    }
}
