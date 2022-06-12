<?php declare(strict_types=1);
namespace froq\test\file;
use froq\file\object\{FileObject, FileObjectException};

class FileObjectTest extends \TestCase
{
    function setUp(): void {
        $this->util = $this->util('File');
    }

    function test_openClose() {
        $fo = new FileObject();
        $fo->open($this->util->file('', true));

        $this->assertIsResource($fo->getResource());
        $this->assertFileExists($fo->getResourceFile());

        $fo->close(); // Clean up.

        $this->expectException(FileObjectException::class);
        $this->expectExceptionMessage('No file given & no resource file to process');

        $fo->open();
    }

    function test_saveUnsave() {
        $fo = new FileObject($this->util->file('', true));
        $savedFile = $fo->save(directory: tmp());

        $this->assertFileExists($savedFile);
        $this->assertTrue($fo->unsave($savedFile));
        $this->assertFileNotExists($savedFile);
        $this->assertFalse($fo->unsave($savedFile));
    }

    function test_free() {
        $fo = new FileObject($this->util->file('', true));

        $this->assertTrue($fo->free());
        $this->assertFalse($fo->free());
        $this->assertTrue($fo->isFreed());
        $this->assertFalse($fo->isValid());
    }
}
