<?php declare(strict_types=1);
namespace test\froq\reflection;
use froq\reflection\{ReflectionClosure, ReflectionFunction};

class ReflectionClosureTest extends \TestCase
{
    function testConstructor() {
        $ref = new ReflectionClosure(fn() => 1);
        $this->assertInstanceOf(ReflectionFunction::class, $ref);
    }

    function testMagicGet() {
        $ref = new ReflectionClosure(fn() => 1);
        $this->assertSame(__NAMESPACE__ . '\\{closure}', $ref->name);
        $this->assertSame(__CLASS__, $ref->class);
    }

    function testGetClass() {
        $ref = new ReflectionClosure(fn() => 1);
        $this->assertSame(__CLASS__, $ref->getClass());
    }
}
