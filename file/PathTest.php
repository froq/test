<?php declare(strict_types=1);
namespace test\froq\file;
use froq\file\{Path, PathException, PathInfo, Directory, File, error};

class PathTest extends \TestCase
{
    function init() {
        $this->util = $this->util('file');
    }

    function testConstructor() {
        $name = __FILE__;
        $path = new Path($name);

        $this->assertSame($name, $path->name);
        $this->assertClassOf(PathInfo::class, $path);

        try {
            new Path("");
        } catch (PathException $e) {
            $this->assertSame('Invalid path: Path is empty', $e->getMessage());
            $this->assertSame(error\InvalidPathError::class, $e->getCause()->getClass());
        }

        try {
            new Path("null-byte-\0");
        } catch (PathException $e) {
            $this->assertSame('Invalid path: Path contains NULL-bytes', $e->getMessage());
            $this->assertSame(error\InvalidPathError::class, $e->getCause()->getClass());
        }
    }

    function testMagicString() {
        $name = '/';
        $path = new Path($name);

        $this->assertSame($name, (string) $path);
    }

    function testGetters() {
        $name = '/tmp/froq';
        $path = new Path($name);

        $this->assertSame($name, $path->getName());
        $this->assertSame(['/', '/tmp', '/tmp/froq'], $path->getTree());
        $this->assertEquals([new Path('/'), new Path('/tmp'), new Path('/tmp/froq')], $path->getTree(true));
    }

    function testSplit() {
        $path = new Path('/tmp');
        $paths = $path->split();

        $this->assertCount(2, $paths);
        $this->assertSame(['', 'tmp'], $paths);
    }

    function testJoin() {
        $path = new Path('/');
        $paths = $path->join(['tmp']);

        $this->assertSame('/tmp', $paths);
    }

    function testOpen() {
        $dirPath = new Path($this->util->dirMake());
        $filePath = new Path($this->util->fileMake());

        $this->assertInstanceOf(Directory::class, $dirPath->open());
        $this->assertInstanceOf(File::class, $filePath->open());

        try {
            (new Path('absent-dir'))->open();
        } catch (PathException $e) {
            $this->assertStringContains('No such file or directory', $e->getMessage());
            $this->assertInstanceOf(error\NoFileError::class, $e->getCause());
        }

        try {
            (new Path('absent-file'))->open();
        } catch (PathException $e) {
            $this->assertStringContains('No such file or directory', $e->getMessage());
            $this->assertInstanceOf(error\NoFileError::class, $e->getCause());
        }
    }

    function testToDirectory() {
        $path = new Path($this->util->dirMake());

        $this->assertInstanceOf(Directory::class, $path->toDirectory());
    }

    function testToFile() {
        $path = new Path($this->util->fileMake());

        $this->assertInstanceOf(File::class, $path->toFile());
    }

    function testCount() {
        $path = new Path('/tmp');

        $this->assertCount(2, $path);
        $this->assertSame(2, $path->count());
    }

    function testOf() {
        $path = Path::of('/', 'tmp');

        $this->assertInstanceOf(Path::class, $path);
    }
}
