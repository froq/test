<?php declare(strict_types=1);
namespace test\froq\file;
use froq\file\{Directory, DirectoryList, DirectoryException, Path, PathList, PathInfo, PathObject,
    File, FileList, Stat, error};

class DirectoryTest extends \TestCase
{
    function init() {
        $this->util = $this->util('file');
    }

    function testConstructor() {
        $dir = new Directory($path = $this->util->dirMake());

        $this->assertInstanceOf(PathObject::class, $dir);
        $this->assertInstanceOf(Path::class, $dir->path);
        $this->assertSame($path, $dir->path->name);

        // Argument types (string|Path).
        $this->assertEquals(new Directory($path), new Directory(new Path($path)));

        try {
            new Directory("");
        } catch (DirectoryException $e) {
            $this->assertSame('Invalid path: Path is empty', $e->getMessage());
            $this->assertSame(error\InvalidPathError::class, $e->getCause()->getClass());
        }

        try {
            new Directory("null-byte-\0");
        } catch (DirectoryException $e) {
            $this->assertSame('Invalid path: Path contains NULL-bytes', $e->getMessage());
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
            $this->assertSame('Failed to open directory: No such file or directory', $e->getMessage());
            $this->assertSame(error\NoFileError::class, $e->getCause()->getClass());
        }

        try {
            new Directory(__FILE__, ['open' => true]);
        } catch (DirectoryException $e) {
            $this->assertSame('Cannot use a file as a directory', $e->getMessage());
            $this->assertSame(error\NotADirectoryError::class, $e->getCause()->getClass());
        }

        try {
            new Directory(__FILE__);
        } catch (DirectoryException $e) {
            $this->assertSame('Cannot use a file as a directory', $e->getMessage());
            $this->assertSame(error\NotADirectoryError::class, $e->getCause()->getClass());
        }
    }

    function testRead() {
        $dir = new Directory($this->util->dirMake(), ['open' => true]);

        $this->assertSame(0, $dir->count());

        foreach (range(1, 3) as $i) {
            $dirs[] = $this->util->fileMakeIn($dir->path .'/'. $i);
        }

        $this->assertSame(count($dirs), $dir->count());
        $this->assertSame(count($dirs), count($dir));

        $dir->clear(force: true);
        $dir->close();

        foreach (range('a', 'c') as $c) {
            $files[] = $this->util->fileMakeIn($dir->path .'/'. $c);
        }

        $this->assertSame(dirname($files[0]), $dir->read(fn($entry) => $entry == 'a')[0]);
    }

    function testDirectoryGetters() {
        $path = __DIR__;
        $pathDir = dirname($path);
        $rootPathDir = dirname($path, substr_count($path, DIRECTORY_SEPARATOR) - 1);
        $parentPathDir = dirname($path, 1);
        $dir = new Directory($path);

        $this->assertEquals(new Directory($pathDir), $dir->getDirectory());
        $this->assertEquals(new Directory($rootPathDir), $dir->getRootDirectory());
        $this->assertEquals(new Directory($parentPathDir), $dir->getParentDirectory());
    }

    function testDirectoryMethods() {
        $path = $this->util->dirMake();
        $paths = $this->util->dirMakeIn($path, count: 3);
        $dirs = new DirectoryList(map($paths, fn($p) => new Directory($p)));
        $dir = new Directory($path);

        $this->assertTrue($dir->hasDirectories());
        $this->assertEquals($dirs, $dir->getDirectories());
        $this->assertEquals($paths, $dir->getDirectoryNames()->toArray());

        $basename = basename($paths[0]);

        $this->assertTrue($dir->hasChild($basename));
        $this->assertEquals($dirs[0], $dir->getChild($basename));
        $this->assertInstanceOf(Directory::class, $dir->getChild($basename));

        // Aliases.
        $this->assertTrue($dir->hasChildren());
        $this->assertCount(3, $dir->getChildren());
        $this->assertEquals($paths, $dir->getChildrenNames()->toArray());
    }

    function testFileMethods() {
        $path = $this->util->dirMake();
        $paths = $this->util->fileMakeIn($path, count: 3);
        $files = new FileList(map($paths, fn($p) => new File($p)));
        $dir = new Directory($path);

        $this->assertTrue($dir->hasFiles());
        $this->assertEquals($files, $dir->getFiles());
        $this->assertEquals($paths, $dir->getFileNames()->toArray());

        $basename = basename($paths[0]);

        $this->assertTrue($dir->hasFile($basename));
        $this->assertEquals($files[0], $dir->getFile($basename));
        $this->assertInstanceOf(File::class, $dir->getFile($basename));
    }

    function testSubPathMethods() {
        $dir = new Directory(__DIR__);

        $this->assertTrue($dir->hasSubPath('upload'));
        $this->assertInstanceOf(Path::class, $dir->getSubPath('upload'));
    }

    function testParentMethods() {
        $dir = new Directory(__DIR__);

        $this->assertTrue($dir->hasParent());
        $this->assertInstanceOf(Directory::class, $dir->getDirectory());
    }

    function testList() {
        $dir = new Directory(__DIR__);

        $this->assertInstanceOf(PathList::class, $dir->list());
    }

    function testGlob() {
        $dir = new Directory(__DIR__);

        $this->assertInstanceOf(\GlobIterator::class, $dir->glob('*'));
    }

    function testFind() {
        $dir = new Directory(__DIR__);

        $this->assertInstanceOf(\RegexIterator::class, $dir->find('~.*~'));
    }

    function testFindAll() {
        $dir = new Directory(__DIR__);

        $this->assertInstanceOf(\RegexIterator::class, $dir->find('~.*~'));
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
            $this->assertFileExists($path);
        }

        $this->assertInstanceOf(\IteratorAggregate::class, $dir);
        $this->assertInstanceOf(DirectoryList::class, $dir->getIterator());
    }

    // /** Inherit Methods */

    function testGetPath() {
        $path = __DIR__;
        $dir = new Directory($path);

        $this->assertSame($path, $dir->path->name);
        $this->assertSame($path, $dir->getPath()->getName());
        $this->assertEquals(new Path($path), $dir->getPath());
    }

    function testGetPathInfo() {
        $path = __DIR__;
        $dir = new Directory($path);

        $this->assertEquals(new PathInfo($path), $dir->getPathInfo());
    }

    function testGetStat() {
        $path = __DIR__;
        $file = new Directory($path);

        $this->assertEquals(new Stat($path), $file->getStat());
    }

    function testExists() {
        $path = $this->util->dirMake();
        $dir = new Directory($path);

        $this->assertTrue($dir->exists());

        rmdir($path);

        $this->assertFalse($dir->exists());
    }

    function testOkay() {
        $path = $this->util->dirMake();
        $file = new Directory($path);

        $this->assertTrue($file->okay(read: true));
        $this->assertTrue($file->okay(write: true));
        $this->assertTrue($file->okay(execute: true));

        chmod($path, 0);

        $this->assertFalse($file->okay(read: true));
        $this->assertFalse($file->okay(write: true));
        $this->assertFalse($file->okay(execute: true));
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
