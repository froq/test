<?php declare(strict_types=1);
namespace test\froq\reflection;
use froq\reflection\{ReflectionFunction, ReflectionNamespace};

class ReflectionFunctionTest extends \TestCase
{
    function testGetters() {
        $ref = new ReflectionFunction('strlen');
        $this->assertSame('strlen', $ref->getName());
        $this->assertSame('strlen', $ref->getLongName());

        $this->assertSame('function', $ref->getType());
        $this->assertSame('int', $ref->getReturnType()->getName());

        $this->assertSame('', $ref->getNamespace());
        $this->assertInstanceOf(ReflectionNamespace::class, $ref->getDeclaringNamespace());

        $this->assertNull($ref->getClass());
        $this->assertNull($ref->getDeclaringClass()?->name);
    }

    function testAttributeMethods() {
        $ref = new ReflectionFunction('strlen');
        $this->assertInstanceOf(\Set::class, $ref->attributes());
        $this->assertCount(0, $ref->attributes());
        $this->assertFalse($ref->hasAttribute('Foo'));
        $this->assertNull($ref->getAttribute('Foo'));
        $this->assertSame([], $ref->getAttributeNames());
    }

    function testParameterMethods() {
        $ref = new ReflectionFunction('strlen');
        $this->assertInstanceOf(\Set::class, $ref->parameters());
        $this->assertCount(1, $ref->parameters());
        $this->assertTrue($ref->hasParameter(0));
        $this->assertTrue($ref->hasParameter('string'));
        $this->assertFalse($ref->hasParameter(3));
        $this->assertFalse($ref->hasParameter('foo'));
        $this->assertNotNull($ref->getParameter(0));
        $this->assertNull($ref->getParameter(3));
        $this->assertNotNull($ref->getParameter('string'));
        $this->assertNull($ref->getParameter('foo'));
        $this->assertCount(1, $ref->getParameters());
        $this->assertSame(1, $ref->getParametersCount());
        $this->assertSame(['string'], $ref->getParameterNames());
        $this->assertSame([null], $ref->getParameterValues());
    }

    function testDocumentMethods() {
        $ref = new ReflectionFunction('strlen');
        $this->assertNull($ref->getDocument());
        $this->assertNull($ref->getDocumentDescription());
    }
}
