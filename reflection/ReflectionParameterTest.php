<?php declare(strict_types=1);
namespace test\froq\reflection;
use froq\reflection\{ReflectionParameter, ReflectionType};

class ReflectionParameterTest extends \TestCase
{
    function test_valueMethods() {
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

    function test_typeMethods() {
        $fun = function ($a, int $b = 0) {};

        $ref = new ReflectionParameter($fun, 'a');
        $this->assertNull($ref->getType());
        $this->assertNull($ref->getTypes());

        $ref = new ReflectionParameter($fun, 'b');
        $this->assertNotNull($ref->getType());
        $this->assertIsArray($ref->getTypes());
        $this->assertInstanceOf(ReflectionType::class, $ref->getType());
    }
}
