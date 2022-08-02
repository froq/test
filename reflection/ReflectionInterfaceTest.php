<?php declare(strict_types=1);
namespace test\froq\reflection;
use froq\reflection\{ReflectionClass, ReflectionInterface};

class ReflectionInterfaceTest extends \TestCase
{
    function test_constructor() {
        $ref = new ReflectionInterface(\Throwable::class);
        $this->assertFalse($ref->isClass());
        $this->assertSame('interface', $ref->getType());
        $this->assertInstanceOf(ReflectionClass::class, $ref);
        $this->assertInstanceOf(\ReflectionClass::class, $ref);

        $this->expectException(\ReflectionException::class);
        $this->expectExceptionMessage('Interface "TestCase" does not exist');
        new ReflectionInterface(\TestCase::class);
    }
}
