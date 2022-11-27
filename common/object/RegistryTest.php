<?php declare(strict_types=1);
namespace test\froq\common\object;
use froq\common\object\{Registry, RegistryException};

class RegistryTest extends \TestCase
{
    function test_storageMethods() {
        [$id, $object] = $this->getMock();

        $this->assertFalse(Registry::has($id));
        $this->assertNull(Registry::get($id));

        Registry::set($id, $object);

        $this->assertTrue(Registry::has($id));
        $this->assertSame($object, Registry::get($id));

        Registry::remove($id);

        $this->assertFalse(Registry::has($id));
        $this->assertNull(Registry::get($id));
    }

    function test_lockedStates() {
        [$id, $object] = $this->getMock();

        Registry::set($id, $object);

        try {
            Registry::set($id, $object);
        } catch (RegistryException $e) {
            $this->assertStringContains('registered and locked', $e->getMessage());
        }

        [$id, $object] = $this->getMock();

        Registry::replace($id, $object); // No error.

        [$id, $object] = $this->getMock();

        Registry::set($id, $object, locked: false);
        Registry::set($id, $object); // No error.

        $this->assertSame($object, Registry::get($id));
    }

    private function getMock() {
        return [(string) rand(), (object) ['x' => 1]];
    }
}
