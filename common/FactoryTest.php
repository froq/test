<?php
namespace froq\test\common;
use froq\common\object\{Factory, FactoryException};

class FactoryTest extends \PHPUnit\Framework\TestCase
{
    function test_noClassException() {
        try {
            Factory::init('nonexistent');
        } catch (\Throwable $e) {
            $this->assertInstanceOf(FactoryException::class, $e);
            $this->assertStringContainsString('No class exists', $e->getMessage());
        }

        try {
            Factory::initOnce('nonexistent');
        } catch (\Throwable $e) {
            $this->assertInstanceOf(FactoryException::class, $e);
            $this->assertStringContainsString('No class exists', $e->getMessage());
        }
    }

    function test_initMethod() {
        $class = 'Error';
        $classArgs = ['code' => 1, 'message' => 'Test!'];
        $object = Factory::init($class, ...$classArgs);

        $this->assertSame($classArgs['code'], $object->getCode());
        $this->assertSame($classArgs['message'], $object->getMessage());
    }

    function test_initOnceMethod() {
        $class = 'Error';
        $classArgs = ['code' => 1, 'message' => 'Test!'];
        $object = Factory::initOnce($class, ...$classArgs);

        $this->assertSame($classArgs['code'], $object->getCode());
        $this->assertSame($classArgs['message'], $object->getMessage());

        $this->assertSame($object, Factory::initOnce($class, ...$classArgs));
        $this->assertSame($object, Factory::initOnce($class, ...$classArgs));
        $this->assertSame($object, Factory::initOnce($class, ...$classArgs));
    }
}
