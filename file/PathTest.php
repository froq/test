<?php declare(strict_types=1);
namespace test\froq\file;
use froq\file\{Path, PathException, PathInfo, error};

class PathTest extends \TestCase
{
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

    function testStringCast() {
        $name = __FILE__;
        $path = new Path($name);

        $this->assertSame($name, (string) $path);
    }

    function testGetters() {
        $name = __FILE__;
        $path = new Path($name);

        $this->assertSame($name, $path->getName());
    }
}
