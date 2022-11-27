<?php declare(strict_types=1);
namespace test\froq\reflection;
use froq\reflection\{ReflectionClass, ReflectionTrait};

class ReflectionTraitTest extends \TestCase
{
    function test_constructor() {
        $ref = new ReflectionTrait('froq\common\trait\ThrownableTrait');
        $this->assertFalse($ref->isClass());
        $this->assertSame('trait', $ref->getType());
        $this->assertInstanceOf(ReflectionClass::class, $ref);
        $this->assertInstanceOf(\ReflectionClass::class, $ref);

        $this->expectException(\ReflectionException::class);
        $this->expectExceptionMessage('Trait "Foo_Bar_Baz" does not exist');
        new ReflectionTrait('Foo_Bar_Baz');
    }
}
