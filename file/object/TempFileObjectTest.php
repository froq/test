<?php declare(strict_types=1);
namespace test\froq\file\object;
use froq\file\object\{TempFileObject, TempFileObjectException};

class TempFileObjectTest extends \TestCase
{
    function test_constructor() {
        $fo = new TempFileObject();

        $this->assertIsResource($fo->getResource());
        $this->assertFileExists($fo->getResourceFile());

        $fo->free(); // Clean up.

        $this->assertIsNotResource($fo->getResource());
        $this->assertFileNotExists((string) $fo->getResourceFile());
    }

    function test_openClose() {
        $fo = new TempFileObject();

        try {
            $fo->open();
        } catch (TempFileObjectException $e) {
            $this->assertSame(sprintf(
                'Method open() is not available for %s classes', TempFileObject::class
            ), $e->getMessage());
        }

        try {
            $fo->close();
        } catch (TempFileObjectException $e) {
            $this->assertSame(sprintf(
                'Method close() is not available for %s classes', TempFileObject::class
            ), $e->getMessage());
        }
    }
}
