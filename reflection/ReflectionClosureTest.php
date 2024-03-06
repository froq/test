<?php declare(strict_types=1);
namespace test\froq\reflection;
use froq\reflection\{ReflectionClosure, ReflectionFunction, ReflectionClass};

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

    function testGetters() {
        $ref = new ReflectionClosure(fn() => 1);
        $this->assertSame('closure', $ref->getType());
        $this->assertSame(__NAMESPACE__ . '\\{closure}', $ref->getName());
        $this->assertSame(__CLASS__ . '#{closure}', $ref->getLongName());
        $this->assertSame('{closure}', $ref->getShortName());
        $this->assertSame(__CLASS__, $ref->getClass());
        $this->assertSame(__CLASS__, $ref->getClosureCalledClass()->name);
        $this->assertInstanceOf(ReflectionClass::class, $ref->getClosureCalledClass());
        $this->assertSame(__CLASS__, $ref->getClosureScopeClass()->name);
        $this->assertInstanceOf(ReflectionClass::class, $ref->getClosureScopeClass());
    }
}
