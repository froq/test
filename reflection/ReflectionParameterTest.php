<?php declare(strict_types=1);
namespace test\froq\reflection;
use froq\reflection\{ReflectionParameter, ReflectionMethod, ReflectionFunction, ReflectionType};

class ReflectionParameterTest extends \TestCase
{
    function testGetters($a = null) {
        $fun = function ($a) {};

        $ref = new ReflectionParameter($fun, 'a');
        $this->assertSame(__CLASS__, $ref->getClass());
        $this->assertSame(__CLASS__, $ref->getDeclaringClass()->name);
        $this->assertNull($ref->getDeclaringMethod()?->name);
        $this->assertNotNull($ref->getDeclaringFunction()?->name);

        $ref = new ReflectionParameter(__METHOD__, 'a');
        $this->assertSame(__CLASS__, $ref->getClass());
        $this->assertSame(__CLASS__, $ref->getDeclaringClass()->name);
        $this->assertInstanceOf(ReflectionMethod::class, $ref->getDeclaringMethod());
        $this->assertInstanceOf(ReflectionMethod::class, $ref->getDeclaringFunction());

        $ref = new ReflectionParameter('strlen', 'string');
        $this->assertNull($ref->getClass());
        $this->assertNull($ref->getDeclaringClass()?->name);
        $this->assertNull($ref->getDeclaringMethod()?->name);
        $this->assertInstanceOf(ReflectionFunction::class, $ref->getDeclaringFunction());
    }

    function testValueMethods() {
        $fun = function ($a, $b = 0, $c = \PRECISION) {};

        $ref = new ReflectionParameter($fun, 'a');
        $this->assertFalse($ref->hasDefaultValue());
        $this->assertNull($ref->getDefaultValue());

        $ref = new ReflectionParameter($fun, 'b');
        $this->assertTrue($ref->hasDefaultValue());
        $this->assertNotNull($ref->getDefaultValue());
        $this->assertFalse($ref->isDefaultValueConstant());
        $this->assertNull($ref->getDefaultValueConstantName());

        $ref = new ReflectionParameter($fun, 'c');
        $this->assertTrue($ref->hasDefaultValue());
        $this->assertNotNull($ref->getDefaultValue());
        $this->assertTrue($ref->isDefaultValueConstant());
        $this->assertNotNull($ref->getDefaultValueConstantName());
    }

    function testTypeMethods() {
        $fun = function ($a, int $b = 0) {};

        $ref = new ReflectionParameter($fun, 'a');
        $this->assertNull($ref->getType());
        $this->assertIsArray($ref->getTypes());

        $ref = new ReflectionParameter($fun, 'b');
        $this->assertNotNull($ref->getType());
        $this->assertInstanceOf(ReflectionType::class, $ref->getType());
        $this->assertInstanceOf(ReflectionType::class, $ref->getTypes()[0]);
    }
}
