<?php declare(strict_types=1);
namespace test\froq\common\object;
use froq\common\object\{Factory, FactoryException};

class FactoryTest extends \TestCase
{
    function test_noClassException() {
        try {
            Factory::init('nonexistent');
        } catch (FactoryException $e) {
            $this->assertStringContains('No class exists', $e->getMessage());
        }

        try {
            Factory::initOnce('nonexistent');
        } catch (FactoryException $e) {
            $this->assertStringContains('No class exists', $e->getMessage());
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
