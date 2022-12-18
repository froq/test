<?php declare(strict_types=1);
namespace test\froq\reflection;
use froq\reflection\{
    Reflection, ReflectionCallable, ReflectionClass, ReflectionClassConstant,
    ReflectionFunction, ReflectionInterface, ReflectionMethod, ReflectionNamespace,
    ReflectionObject, ReflectionParameter, ReflectionProperty, ReflectionTrait, ReflectionType
};

class ReflectionTest extends \TestCase
{
    function testGetVisibility() {
        $this->assertSame('public', Reflection::getVisibility(new \ReflectionMethod(CTest::class, 'publicMethod')));
        $this->assertSame('private', Reflection::getVisibility(new \ReflectionMethod(CTest::class, 'privateMethod')));
        $this->assertSame('protected', Reflection::getVisibility(new \ReflectionMethod(CTest::class, 'protectedMethod')));
    }

    function testReflectMethods() {
        $this->assertInstanceOf(ReflectionCallable::class, Reflection::reflectCallable('strlen'));
        $this->assertInstanceOf(ReflectionClass::class, Reflection::reflectClass(CTest::class));
        $this->assertInstanceOf(ReflectionClassConstant::class, Reflection::reflectClassConstant(CTest::class, 'NS'));
        $this->assertInstanceOf(ReflectionFunction::class, Reflection::reflectFunction('strlen'));
        $this->assertInstanceOf(ReflectionInterface::class, Reflection::reflectInterface(ITest::class));
        $this->assertInstanceOf(ReflectionMethod::class, Reflection::reflectMethod(CTest::class, 'function'));
        $this->assertInstanceOf(ReflectionNamespace::class, Reflection::reflectNamespace(CTest::NS));
        $this->assertInstanceOf(ReflectionObject::class, Reflection::reflectObject(new CTest()));
        $this->assertInstanceOf(ReflectionParameter::class, Reflection::reflectParameter([CTest::class, 'function'], 'a'));
        $this->assertInstanceOf(ReflectionProperty::class, Reflection::reflectProperty(CTest::class, 'x'));
        $this->assertInstanceOf(ReflectionTrait::class, Reflection::reflectTrait(TTest::class));
        $this->assertInstanceOf(ReflectionType::class, Reflection::reflectType('int'));
    }
}

trait TTest {
    var $x;
}

interface ITest {
    const NS = __namespace__;
}

class CTest implements ITest {
    use TTest;

    function function($a = null) {}

    public function publicMethod() {}
    private function privateMethod() {}
    protected function protectedMethod() {}
}
