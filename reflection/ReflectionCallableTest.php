<?php declare(strict_types=1);
namespace test\froq\reflection;
use froq\reflection\{ReflectionCallable, ReflectionAttribute};
use froq\reflection\document\CallableDocument;

class ReflectionCallableTest extends \TestCase
{
    function test_magicGet() {
        $ref = new ReflectionCallable(__method__);
        $this->assertSame('test_magicGet', $ref->name);
        $this->assertSame('test\froq\reflection\ReflectionCallableTest', $ref->class);

        $this->expectException(\ReflectionException::class);
        $this->expectExceptionMessage('Undefined property froq\reflection\ReflectionCallable::$foo / ReflectionMethod::$foo');
        $ref->foo;
    }

    function test_magicCall() {
        $ref = new ReflectionCallable(__method__);
        $this->assertSame('test_magicCall', $ref->getName());
        $this->assertSame('test\froq\reflection\ReflectionCallableTest', $ref->getClass());

        $this->expectException(\ReflectionException::class);
        $this->expectExceptionMessage('Undefined method froq\reflection\ReflectionCallable::getFoo() / ReflectionMethod::getFoo()');
        $ref->getFoo();
    }

    function test_magicToString() {
        $ref = new ReflectionCallable(__method__);
        $this->assertIsString((string) $ref);
        $this->assertStringContains('test_magicToString', (string) $ref);
    }

    function test_isMethods() {
        $ref = new ReflectionCallable(__method__);
        $this->assertTrue($ref->isMethod());
        $this->assertFalse($ref->isFunction());
    }

    function test_getters() {
        $ref = new ReflectionCallable($this, 'theShinyMethod');
        $this->assertSame('theShinyMethod', $ref->getName());
        $this->assertSame(__class__, $ref->getClass());
        $this->assertSame(__class__, $ref->getDeclaringClass()->name);
        $this->assertSame('public', $ref->getVisibility());
        $this->assertSame(['final', 'public', 'static'], $ref->getModifierNames());
        $this->assertSame('int|float|null', $ref->getReturnType()->getName());
    }

    function test_attributeMethods() {
        $ref = new ReflectionCallable($this, 'theShinyMethod');
        $this->assertInstanceOf(\Set::class, $ref->attributes());
        $this->assertCount(3, $ref->attributes());
        $this->assertTrue($ref->hasAttribute(__namespace__ . '\Foo'));
        $this->assertFalse($ref->hasAttribute('Foo'));
        $this->assertNotNull($ref->getAttribute(__namespace__ . '\Foo'));
        $this->assertNull($ref->getAttribute('Foo'));
        $this->assertSame(array_map(fn($name) => __namespace__ .'\\'. $name, ['Foo', 'Bar', 'Baz']),
            $ref->getAttributeNames());

        $attr = $ref->getAttribute($name = __namespace__ . '\Foo');
        $this->assertInstanceOf(ReflectionAttribute::class, $attr);
        $this->assertSame($name, $attr->name);
        $this->assertSame(['arg' => 1], $attr->arguments);
        $this->assertSame(1, $attr->getArgument('arg'));
        $this->assertSame($name, $attr->getName());
        $this->assertSame('Foo', $attr->getShortName());
        $this->assertSame(__namespace__, $attr->getNamespace());
    }

    function test_interfaceMethods() {
        $ref = new ReflectionCallable($this, 'theShinyMethod');
        $this->assertInstanceOf(\Set::class, $ref->interfaces());
        $this->assertCount(0, $ref->interfaces());
        $this->assertNull($ref->getInterface());
        $this->assertNull($ref->getInterface('Foo'));
        $this->assertSame([], $ref->getInterfaces());
        $this->assertNull($ref->getInterfaceName());
        $this->assertSame([], $ref->getInterfaceNames());
    }

    function test_traitMethods() {
        $ref = new ReflectionCallable($this, 'theShinyMethod');
        $this->assertInstanceOf(\Set::class, $ref->traits());
        $this->assertCount(0, $ref->traits());
        $this->assertNull($ref->getTrait());
        $this->assertNull($ref->getTrait('Foo'));
        $this->assertSame([], $ref->getTraits());
        $this->assertNull($ref->getTraitName());
        $this->assertSame([], $ref->getTraitNames());
    }

    function test_parameterMethods() {
        $ref = new ReflectionCallable($this, 'theShinyMethod');
        $this->assertInstanceOf(\Set::class, $ref->parameters());
        $this->assertCount(2, $ref->parameters());
        $this->assertTrue($ref->hasParameter(0));
        $this->assertTrue($ref->hasParameter('arg1'));
        $this->assertFalse($ref->hasParameter(3));
        $this->assertFalse($ref->hasParameter('arg3'));
        $this->assertNotNull($ref->getParameter(0));
        $this->assertNull($ref->getParameter(3));
        $this->assertNotNull($ref->getParameter('arg1'));
        $this->assertNull($ref->getParameter('arg3'));
        $this->assertCount(2, $ref->getParameters());
        $this->assertSame(2, $ref->getParametersCount());
        $this->assertSame(['arg1', 'arg2'], $ref->getParameterNames());
        $this->assertSame([null, 0.0], $ref->getParameterValues());
    }

    function test_documentMethods() {
        $ref = new ReflectionCallable($this, 'theShinyMethod');
        $this->assertSame('The Shiny Method.', $ref->getDocumentDescription());

        $doc = $ref->getDocument();
        $this->assertInstanceOf(CallableDocument::class, $doc);
        $this->assertSame('The Shiny Method.', $doc->getDescription());
        $this->assertCount(2, $doc->getParameters());
        $this->assertSame('arg1', $doc->getParameter(0)->getName());
        $this->assertSame('int|float|null', $doc->getReturn()->getType());
        $this->assertSame('Error', $doc->getThrows()->getType());
        $this->assertSame('Exception', $doc->getCauses()->getType());
    }

    /**
     * The Shiny Method.
     *
     * @param  int $arg1 The arg 1.
     * @param  int $arg2 The arg 2.
     * @return int|float|null
     * @throws Error
     * @causes Exception
     */
    #[Foo(arg: 1), Bar(), Baz]
    public static final function theShinyMethod(
        int $arg1, float $arg2 = 0.0,
    ): int|float|null {
        return null;
    }
}
