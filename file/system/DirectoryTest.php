<?php declare(strict_types=1);
namespace froq\test\file\system;
use froq\file\system\{Directory, DirectoryException};

class DirectoryTest extends \TestCase
{
    function setUp(): void {
        $this->util = $this->util('File');
    }

    function test_constructor() {
        $path = __file__;

        $this->expectException(DirectoryException::class);
        $this->expectExceptionMessage("Given path is a file [path: {$path}]");
        new Directory($path);
    }

    function test_okay() {
        $this->assertTrue((new Directory(__dir__))->okay());
        $this->assertFalse((new Directory('absent-dir'))->okay());
    }

    function test_glob() {
        $dir = $this->util->dirMake();
        $fs = new Directory($dir);

        $this->assertCount(0, $fs->glob('/*'));
        foreach (range(1, 3) as $id) {
            touch($dir .'/'. $id);
        }
        $this->assertCount(3, $fs->glob('/*'));
    }

    function test_getDirs() {
        $dir = $this->util->dirMake();
        $fs = new Directory($dir);

        $this->assertCount(0, $fs->getDirs());
        foreach (range(1, 3) as $id) {
            dirmake($dir .'/'. $id);
        }
        $this->assertCount(3, $fs->getDirs());
    }

    function test_getFiles() {
        $dir = $this->util->dirMake();
        $fs = new Directory($dir);

        $this->assertCount(0, $fs->getFiles());
        foreach (range(1, 3) as $id) {
            filemake($dir .'/'. $id);
        }
        $this->assertCount(3, $fs->getFiles());
    }

    function test_empty() {
        $dir = $this->util->dirMake();
        $fs = new Directory($dir);

        $this->assertTrue($fs->empty(sure: true));
        foreach (range(1, 3) as $id) {
            dirmake($dir .'/'. $id);
        }
        foreach (range(1, 3) as $id) {
            filemake($dir .'/'. $id);
        }
        $this->assertTrue($fs->empty(sure: true));

        $this->expectException(DirectoryException::class);
        $this->expectExceptionMessage(sprintf(
            'Be sure before calling %s::%s() and deleting all contents of directory `%s`',
            Directory::class, 'empty', $dir
        ));
        $this->assertTrue($fs->empty(sure: false));
    }

    /* Inherits. */

    function test_getters() {
        $dir = $this->util->dirMake();
        $fs = new Directory($dir);

        $this->assertSame($dir, $fs->path);
        $this->assertSame($dir, $fs->getPath());
        $this->assertSame($dir, $fs->getPathOrig());
        $this->assertSame($dir, $fs->getRealPath());
        $this->assertSame('dir', $fs->getPathType());
        $this->assertSame(dirname($dir), $fs->getDirname());
        $this->assertSame(basename($dir), $fs->getBasename());

        $this->assertSame('directory', $fs->getMime());
        $this->assertSame('dir', $fs->getType());
        $this->assertSame(8192, $fs->getSize());
        $this->assertSame('8KB', $fs->getSize(true));

        $this->assertInstanceOf(\FilesystemIterator::class, $fs->getIterator());
        $this->assertInstanceOf(\DirectoryIterator::class, $fs->getDirectoryIterator());
    }

    function test_checkers() {
        $dir = $this->util->dirMake();
        $fs = new Directory($dir);

        $this->assertTrue($fs->exists());
        $this->assertTrue($fs->isDir());
        $this->assertFalse($fs->isFile());
        $this->assertFalse($fs->isLink());

        $this->assertTrue($fs->isEmpty());
        $this->assertTrue($fs->isReadable());
        $this->assertTrue($fs->isWritable());
        $this->assertTrue($fs->isExecutable());
        $this->assertTrue($fs->isAvailable());
        $this->assertTrue($fs->isAvailableFor('read'));
        $this->assertTrue($fs->hasAccess('read'));
        $this->assertTrue($fs->hasAccess(Directory::MODE_READ));
    }

    function test_modifiers() {
        $dir = $this->util->dirMake();
        $fs = new Directory($dir);

        $this->assertTrue($fs->touch());
        $this->assertTrue($fs->rename($dir . 'new'));
        // Not for dirs.
        $this->assertFalse(@$fs->copy(''));
        $this->assertFalse(@$fs->drop());
    }

    function test_makeRemove() {
        $dir = $this->util->dir();
        $fs = new Directory($dir);

        $this->assertFalse($fs->exists());
        $this->assertTrue($fs->make());
        $this->assertTrue($fs->exists());
        $this->assertTrue($fs->remove());
        $this->assertFalse($fs->exists());
    }

    function test_makeLinkRemoveLink() {
        $dir = $this->util->dirMake();
        $dirLink = $dir . 'link';
        $fs = new Directory($dir);
        $fsl = new Directory($dirLink);

        $this->assertFalse($fsl->exists());
        $this->assertTrue($fs->makeLink($dirLink));
        $this->assertTrue($fsl->exists());
        $this->assertTrue($fs->removeLink($dirLink));
        // $this->assertTrue($fsl->removeLink()); // Or self directly.
        $this->assertFalse($fsl->exists());
    }
}
