<?php declare(strict_types=1);
namespace test\froq\common\object;
use froq\common\object\{Factory, FactoryException};

class FactoryTest extends \TestCase
{
    function testNoClassException() {
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

    function testInitMethod() {
        $class = 'Error';
        $classArgs = ['code' => 1, 'message' => 'Test!'];
        $object = Factory::init($class, ...$classArgs);

        $this->assertSame($classArgs['code'], $object->getCode());
        $this->assertSame($classArgs['message'], $object->getMessage());
    }

    function testInitOnceMethod() {
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
