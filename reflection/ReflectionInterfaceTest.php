<?php declare(strict_types=1);
namespace test\froq\reflection;
use froq\reflection\{ReflectionClass, ReflectionInterface};

class ReflectionInterfaceTest extends \TestCase
{
    function testConstructor() {
        $ref = new ReflectionInterface('Throwable');
        $this->assertFalse($ref->isClass());
        $this->assertSame('interface', $ref->getType());
        $this->assertInstanceOf(ReflectionClass::class, $ref);
        $this->assertInstanceOf(\ReflectionClass::class, $ref);

        $this->expectException(\ReflectionException::class);
        $this->expectExceptionMessage('Interface "Foo_Bar_Baz" does not exist');
        new ReflectionInterface('Foo_Bar_Baz');
    }
}
