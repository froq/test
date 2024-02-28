<?php declare(strict_types=1);
namespace test\froq\reflection;
use froq\reflection\{
    Reflection, ReflectionAttribute, ReflectionCallable, ReflectionClass, ReflectionClassConstant,
    ReflectionFunction, ReflectionInterface, ReflectionMethod, ReflectionNamespace,
    ReflectionObject, ReflectionParameter, ReflectionProperty, ReflectionTrait, ReflectionType
};

class ReflectionTest extends \TestCase
{
    function testGetType() {
        $this->assertSame('enum', Reflection::getType(new \ReflectionEnum(ETest::class)));
        $this->assertSame('attribute', Reflection::getType((new \ReflectionClass(CTest::class))->getAttributes()[0]));
        $this->assertSame('callable', Reflection::getType(new \ReflectionCallable('strlen')));
        $this->assertSame('class', Reflection::getType(new \ReflectionClass($this)));
        $this->assertSame('class-constant', Reflection::getType(new \ReflectionClassConstant(CTest::class, 'NS')));
        $this->assertSame('function', Reflection::getType(new \ReflectionFunction('strlen')));
        $this->assertSame('method', Reflection::getType(new \ReflectionMethod(CTest::class, 'method')));
        $this->assertSame('interface', Reflection::getType(new ReflectionInterface(ITest::class)));
        $this->assertSame('trait', Reflection::getType(new ReflectionTrait(TTest::class)));
        $this->assertSame('object', Reflection::getType(new ReflectionObject($this)));
        $this->assertSame('namespace', Reflection::getType(new ReflectionNamespace('')));
        $this->assertSame('parameter', Reflection::getType(new ReflectionParameter([CTest::class, 'method'], 'a')));
        $this->assertSame('property', Reflection::getType(new ReflectionProperty(CTest::class, 'x')));
        $this->assertSame('type', Reflection::getType(new ReflectionType('int')));
    }

    function testGetVisibility() {
        $this->assertSame('public', Reflection::getVisibility(new \ReflectionMethod(CTest::class, 'publicMethod')));
        $this->assertSame('private', Reflection::getVisibility(new \ReflectionMethod(CTest::class, 'privateMethod')));
        $this->assertSame('protected', Reflection::getVisibility(new \ReflectionMethod(CTest::class, 'protectedMethod')));
    }

    function testReflectMethod() {
        $this->assertInstanceOf(\Reflector::class, Reflection::reflect(CTest::class));
    }

    function testReflectMethods() {
        $this->assertInstanceOf(ReflectionAttribute::class, Reflection::reflectAttribute(
            (new \ReflectionClass(CTest::class))->getAttributes()[0]
        ));
        $this->assertInstanceOf(ReflectionCallable::class, Reflection::reflectCallable('strlen'));
        $this->assertInstanceOf(ReflectionClass::class, Reflection::reflectClass(CTest::class));
        $this->assertInstanceOf(ReflectionClassConstant::class, Reflection::reflectClassConstant(CTest::class, 'NS'));
        $this->assertInstanceOf(ReflectionFunction::class, Reflection::reflectFunction('strlen'));
        $this->assertInstanceOf(ReflectionInterface::class, Reflection::reflectInterface(ITest::class));
        $this->assertInstanceOf(ReflectionMethod::class, Reflection::reflectMethod(CTest::class, 'method'));
        $this->assertInstanceOf(ReflectionNamespace::class, Reflection::reflectNamespace(CTest::NS));
        $this->assertInstanceOf(ReflectionObject::class, Reflection::reflectObject(new CTest()));
        $this->assertInstanceOf(ReflectionParameter::class, Reflection::reflectParameter([CTest::class, 'method'], 'a'));
        $this->assertInstanceOf(ReflectionProperty::class, Reflection::reflectProperty(CTest::class, 'x'));
        $this->assertInstanceOf(ReflectionTrait::class, Reflection::reflectTrait(TTest::class));
        $this->assertInstanceOf(ReflectionType::class, Reflection::reflectType('int'));
    }
}

trait TTest {
    var $x;
}

interface ITest {
    const NS = __NAMESPACE__;
}

enum ETest {
    case A;
}

#[Attr]
class CTest implements ITest {
    use TTest;

    function method($a) {}

    public function publicMethod() {}
    private function privateMethod() {}
    protected function protectedMethod() {}
}
