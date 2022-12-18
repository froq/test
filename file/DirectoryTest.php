<?php declare(strict_types=1);
namespace test\froq\file;
use froq\file\{Directory, DirectoryException, Path, PathInfo, File, error};

class DirectoryTest extends \TestCase
{
    function before() {
        $this->util = $this->util('file');
    }

    function testConstructor() {
        $dir = new Directory($this->util->dirMake());

        $this->assertInstanceOf(Path::class, $dir);

        try {
            new Directory("null-byte-\0");
        } catch (DirectoryException $e) {
            $this->assertSame('Invalid path: Path contains NULL-bytes', $e->getMessage());
            $this->assertSame(error\InvalidPathError::class, $e->getCause()->getClass());
        }

        try {
            new Directory("");
        } catch (DirectoryException $e) {
            $this->assertSame('Invalid path: Path is empty', $e->getMessage());
            $this->assertSame(error\InvalidPathError::class, $e->getCause()->getClass());
        }
    }

    function testOpenCloseValid() {
        $dir = new Directory($this->util->dirMake());

        $this->assertSame($dir, $dir->open());
        $this->assertTrue($dir->valid());
        $this->assertTrue($dir->close());

        $this->assertFalse($dir->valid());
        $this->assertFalse($dir->close());

        try {
            new Directory('absent-dir', ['open' => true]);
        } catch (DirectoryException $e) {
            $this->assertStringContains('No such file', $e->getMessage());
            $this->assertSame(error\NoFileError::class, $e->getCause()->getClass());
        }

        try {
            new Directory(__FILE__, ['open' => true]);
        } catch (DirectoryException $e) {
            $this->assertStringContains('Cannot open a file', $e->getMessage());
            $this->assertSame(error\NotADirectoryError::class, $e->getCause()->getClass());
        }
    }

    function testRead() {
        $dir = new Directory($this->util->dirMake(), ['open' => true]);

        $this->assertSame(0, $dir->count());

        foreach (range(1, 3) as $i) {
            $dirs[] = $this->util->fileMakeIn($dir->getPath() .'/'. $i);
        }

        $this->assertSame(count($dirs), $dir->count());
        $this->assertSame(count($dirs), count($dir));

        $dir->clear(force: true);
        $dir->close();

        foreach (range('a', 'c') as $c) {
            $files[] = $this->util->fileMakeIn($dir->getPath() .'/'. $c);
        }

        $this->assertSame(dirname($files[0]), $dir->read(fn($entry) => $entry == 'a')[0]);
    }

    function testDirectoryGetters() {
        $path = __DIR__;
        $dir = new Directory($path);

        $this->assertEquals(new Directory(dirname($path, substr_count($path, DIRECTORY_SEPARATOR) - 1)),
            $dir->getRootDirectory());
        $this->assertEquals(new Directory(dirname($path, 2)),
            $dir->getParentDirectory());
    }

    function testDirectoryMethods() {
        $path = $this->util->dirMake();
        $paths = $this->util->dirMakeIn($path, count: 3);
        $dirs = new \ArrayIterator(map($paths, fn($p) => new Directory($p)));
        $dir = new Directory($path);

        $this->assertTrue($dir->hasDirectories());
        $this->assertEquals($dirs, $dir->getDirectories());
        $this->assertEquals($paths, (array) $dir->getDirectoryNames());

        // Aliases.
        $this->assertTrue($dir->hasChildren());
        $this->assertCount(3, $dir->getChildren());
        $this->assertEquals($paths, (array) $dir->getChildrenNames());
    }

    function testFileMethods() {
        $path = $this->util->dirMake();
        $paths = $this->util->fileMakeIn($path, count: 3);
        $files = new \ArrayIterator(map($paths, fn($p) => new File($p)));
        $dir = new Directory($path);

        $this->assertTrue($dir->hasFiles());
        $this->assertEquals($files, $dir->getFiles());
        $this->assertEquals($paths, (array) $dir->getFileNames());
    }

    function testCount() {
        $path = $this->util->dirMake();
        $dir = new Directory($path);

        $this->assertCount(0, $dir->getFiles());

        $this->util->fileMakeIn($path, count: 3);

        $this->assertCount(3, $dir->getFiles());
    }

    function testGetIterator() {
        $dir = new Directory(__DIR__);

        foreach ($dir as $path) {
            $this->assertTrue(file_exists($path));
        }

        $this->assertInstanceOf(\ArrayIterator::class, $dir->getIterator());
    }

    /** Inherit Methods */

    function testGetPath() {
        $dir = new Directory(__DIR__);

        $this->assertSame(__DIR__, $dir->getPath());
    }

    function testGetPathInfo() {
        $dir = new Directory(__DIR__);

        $this->assertEquals(new PathInfo(__DIR__), $dir->getPathInfo());
    }

    function testExists() {
        $path = $this->util->dirMake();
        $dir = new Directory($path);

        $this->assertTrue($dir->exists());

        rmdir($path);

        $this->assertFalse($dir->exists());
    }

    function testModeTouch() {
        $path = $this->util->dirMake();
        $dir = new Directory($path);

        $this->assertTrue($dir->mode(0777));
        $this->assertTrue($dir->touch(time()));
    }

    function testLinkUnlink() {
        $path = $this->util->dirMake();
        $dir = new Directory($path);
        $link = $path . 'link';

        $this->assertTrue($dir->link($link));

        $link = new Directory($link);

        $this->assertTrue($link->unlink());

        $this->expectException(DirectoryException::class);
        $this->expectExceptionMessageMatches('~Cannot unlink a directory~');
        $dir->unlink();
    }

    function testRename() {
        $path = $this->util->dirMake();
        $dir = new Directory($path);
        $to = $path . 'to';

        $this->assertTrue($dir->rename($to, force: true));

        $this->expectException(DirectoryException::class);
        $this->expectExceptionMessageMatches('~Cannot rename path~');
        $dir->rename($to, force: false);
    }

    function testRemove() {
        $path = $this->util->dirMake();
        $dir = new Directory($path);

        $this->assertTrue($dir->remove(force: true));

        $this->expectException(DirectoryException::class);
        $this->expectExceptionMessageMatches('~Cannot remove path~');
        $dir->remove(force: false);
    }

    function testCreate() {
        $path = $this->util->dir();
        $dir = new Directory($path);

        $this->assertTrue($dir->create());

        $this->expectException(DirectoryException::class);
        $this->expectExceptionMessageMatches('~Directory exists~');
        $dir->create();
    }

    function testClear() {
        $path = $this->util->dir();
        $dir = new Directory($path);

        $this->util->dirMakeIn($path, count: 3);

        $this->assertSame(3, $dir->count());
        $this->assertTrue($dir->clear(force: true));
        $this->assertSame(0, $dir->count());
    }

    function testDrop() {
        $dir = new Directory($this->util->dirMake());

        $this->assertTrue($dir->drop(force: true));

        $this->expectException(DirectoryException::class);
        $this->expectExceptionMessageMatches('~Cannot drop path~');
        $dir->drop(force: false);
    }
}
