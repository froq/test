<?php declare(strict_types=1);
namespace test\froq\reflection;
use froq\reflection\ReflectionType;

class ReflectionTypeTest extends \TestCase
{
    function test_constructor() {
        new ReflectionType('int'); // OK.

        $this->expectException(\ReflectionException::class);
        $this->expectExceptionMessage('No name given');
        new ReflectionType('');
    }

    function test_magicGet() {
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

    function test_getterMethods() {
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

    function test_checkerMethods() {
        $ref = new ReflectionType('int');
        $this->assertTrue($ref->isBuiltin());
        $this->assertTrue($ref->isNamed());
        $this->assertFalse($ref->isUnion());
        $this->assertFalse($ref->isIntersection());
        $this->assertFalse($ref->isNullable());
        $this->assertFalse($ref->allowsNull());
        $this->assertTrue($ref->contains('int'));

        $ref = new ReflectionType('?int');
        $this->assertTrue($ref->isBuiltin());
        $this->assertTrue($ref->isNamed());
        $this->assertFalse($ref->isUnion());
        $this->assertFalse($ref->isIntersection());
        $this->assertTrue($ref->isNullable());
        $this->assertTrue($ref->allowsNull());
        $this->assertTrue($ref->contains('null'));

        $ref = new ReflectionType('int|float|null');
        $this->assertFalse($ref->isBuiltin());
        $this->assertFalse($ref->isNamed());
        $this->assertTrue($ref->isUnion());
        $this->assertFalse($ref->isIntersection());
        $this->assertTrue($ref->isNullable());
        $this->assertTrue($ref->allowsNull());
        $this->assertTrue($ref->contains('null'));
    }
}