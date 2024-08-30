<?php declare(strict_types=1);
namespace test\froq\file;
use froq\file\{File, TempFile};

class TempFileTest extends \TestCase
{
    function testConstructor() {
        $file = new TempFile();

        $this->assertInstanceOf(File::class, $file);

        $this->assertFileExists($file->getPathName());
        $this->assertTrue($file->close()); // Delete.
        $this->assertFileNotExists($file->getPathName());
        $this->assertFalse($file->delete()); // Already done.


        $file = new TempFile(drop: false);
        $file->close(); // No delete.

        $this->assertFileExists($file->getPathName());
        $this->assertTrue($file->delete());
        $this->assertFileNotExists($file->getPathName());
    }

    function testConstructorWithOptions() {
        $file = new TempFile(options: ['drop' => false]);

        $this->assertFileExists($file->getPathName());
        $this->assertTrue($file->close()); // No delete.
        $this->assertFileExists($file->getPathName());

        $this->assertTrue($file->delete()); // Delete.
        $this->assertFileNotExists($file->getPathName());

        $prefix = 'test-file-';
        $file = new TempFile(options: ['prefix' => $prefix]);

        $this->assertStringStartsWith($prefix, $file->path->filename);
    }
}
