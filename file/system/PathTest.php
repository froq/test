<?php declare(strict_types=1);
namespace test\froq\file\system;
use froq\file\system\{Path, PathException, File, Directory};
use froq\file\object\FileObject;

class PathTest extends \TestCase
{
    function setUp(): void {
        $this->util = $this->util('file');
    }

    function test_constructor() {
        $path = __dir__;

        $this->expectException(PathException::class);
        $this->expectExceptionMessage("Unmatched types 'file' - 'dir'");
        new Path($path, 'file');
    }

    function test_okay() {
        $this->assertTrue((new Path(__file__))->okay());
        $this->assertFalse((new Path('absent-path'))->okay());
    }

    function test_openFile() {
        $file = $this->util->fileMake();
        $fs = new Path($file);

        $this->assertInstanceOf(FileObject::class, $fs->openFile());
    }

    function test_makeRemoveFile() {
        $file = $this->util->file();
        $fs = new Path($file);

        $this->assertTrue($fs->makeFile());
        $this->assertTrue($fs->removeFile());
    }

    function test_makeRemoveDir() {
        $dir = $this->util->dir();
        $fs = new Path($dir);

        $this->assertTrue($fs->makeDirectory());
        $this->assertTrue($fs->removeDirectory());
        // Aliases.
        $this->assertTrue($fs->makeDir());
        $this->assertTrue($fs->removeDir());
    }

    function test_initializers() {
        $this->assertInstanceOf(File::class, (new Path(__file__))->toFile());
        $this->assertInstanceOf(Directory::class, (new Path(__dir__))->toDirectory());
    }

    /* Inherits. */

    function test_getters() {
        $file = $this->util->fileMake();
        $fs = new Path($file);

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
        $fs = new Path($file);

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
        $this->assertTrue($fs->hasAccess(Path::MODE_READ));
    }

    function test_modifiers() {
        $file = $this->util->file();
        $fileCopy = $file . 'copy';
        $fs = new Path($file);
        $fsc = new Path($fileCopy);

        $this->assertTrue($fs->touch());
        $this->assertTrue($fs->copy($fileCopy));
        $this->assertTrue($fsc->rename($fileCopy . 'new'));
        $this->assertTrue($fs->drop());
    }

    function test_makeRemove() {
        $file = $this->util->file();
        $fs = new Path($file, type: 'file');

        $this->assertFalse($fs->exists());
        $this->assertTrue($fs->make());
        $this->assertTrue($fs->exists());
        $this->assertTrue($fs->remove());
        $this->assertFalse($fs->exists());
    }

    function test_makeLinkRemoveLink() {
        $file = $this->util->fileMake();
        $fileLink = $file . 'link';
        $fs = new Path($file);
        $fsl = new Path($fileLink);

        $this->assertFalse($fsl->exists());
        $this->assertTrue($fs->makeLink($fileLink));
        $this->assertTrue($fsl->exists());
        $this->assertTrue($fs->removeLink($fileLink));
        // $this->assertTrue($fsl->removeLink()); // Or self directly.
        $this->assertFalse($fsl->exists());
    }
}
