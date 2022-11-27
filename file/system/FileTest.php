<?php declare(strict_types=1);
namespace test\froq\file\system;
use froq\file\system\{File, FileException};
use froq\file\object\FileObject;

class FileTest extends \TestCase
{
    function setUp(): void {
        $this->util = $this->util('file');
    }

    function test_constructor() {
        $path = __dir__;

        $this->expectException(FileException::class);
        $this->expectExceptionMessage("Given path is a directory [path: {$path}]");
        new File($path);
    }

    function test_okay() {
        $this->assertTrue((new File(__file__))->okay());
        $this->assertFalse((new File('absent-file'))->okay());
    }

    function test_empty() {
        $file = $this->util->fileMake();
        $fs = new File($file);

        $this->assertTrue($fs->empty(sure: true));

        $this->expectException(FileException::class);
        $this->expectExceptionMessage(sprintf(
            "Be sure before calling %s::%s() and deleting all contents of file '%s'",
            File::class, 'empty', $file
        ));
        $this->assertTrue($fs->empty(sure: false));
    }

    function test_open() {
        $file = $this->util->fileMake();
        $fs = new File($file);

        $this->assertInstanceOf(FileObject::class, $fs->open());
    }

    function test_setGetContents() {
        $file = $this->util->fileMake();
        $fs = new File($file);

        $this->assertSame('', $fs->getContents());
        $this->assertTrue($fs->setContents('Hello!'));
        $this->assertSame('Hello!', $fs->getContents());
    }

    /* Inherits. */

    function test_getters() {
        $file = $this->util->fileMake();
        $fs = new File($file);

        $this->assertSame($file, $fs->path);
        $this->assertSame($file, $fs->getPath());
        $this->assertSame($file, $fs->getPathOrig());
        $this->assertSame($file, $fs->getRealPath());
        $this->assertSame('file', $fs->getPathType());
        $this->assertSame(dirname($file), $fs->getDirname());
        $this->assertSame(basename($file), $fs->getBasename());

        $this->assertSame('application/x-empty', $fs->getMime());
        $this->assertSame('file', $fs->getType());
        $this->assertSame(0, $fs->getSize());
        $this->assertSame('0B', $fs->getSize(true));
    }

    function test_checkers() {
        $file = $this->util->fileMake();
        $fs = new File($file);

        $this->assertTrue($fs->exists());
        $this->assertTrue($fs->isFile());
        $this->assertFalse($fs->isDir());
        $this->assertFalse($fs->isLink());

        $this->assertTrue($fs->isEmpty());
        $this->assertTrue($fs->isReadable());
        $this->assertTrue($fs->isWritable());
        $this->assertTrue($fs->isExecutable());
        $this->assertTrue($fs->isAvailable());
        $this->assertTrue($fs->isAvailableFor('read'));
        $this->assertTrue($fs->hasAccess('read'));
        $this->assertTrue($fs->hasAccess(File::MODE_READ));
    }

    function test_modifiers() {
        $file = $this->util->file();
        $fileCopy = $file . 'copy';
        $fs = new File($file);
        $fsc = new File($fileCopy);

        $this->assertTrue($fs->touch());
        $this->assertTrue($fs->copy($fileCopy));
        $this->assertTrue($fsc->rename($fileCopy . 'new'));
        $this->assertTrue($fs->drop());
    }

    function test_makeRemove() {
        $file = $this->util->file();
        $fs = new File($file);

        $this->assertFalse($fs->exists());
        $this->assertTrue($fs->make());
        $this->assertTrue($fs->exists());
        $this->assertTrue($fs->remove());
        $this->assertFalse($fs->exists());
    }

    function test_makeLinkRemoveLink() {
        $file = $this->util->fileMake();
        $fileLink = $file . 'link';
        $fs = new File($file);
        $fsl = new File($fileLink);

        $this->assertFalse($fsl->exists());
        $this->assertTrue($fs->makeLink($fileLink));
        $this->assertTrue($fsl->exists());
        $this->assertTrue($fs->removeLink($fileLink));
        // $this->assertTrue($fsl->removeLink()); // Or self directly.
        $this->assertFalse($fsl->exists());
    }
}
