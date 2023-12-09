<?php declare(strict_types=1);
namespace test\froq\file;
use froq\file\{PathInfo, PathInfoException, error};

class PathInfoTest extends \TestCase
{
    function init() {
        $this->util = $this->util('file');
    }

    function testConstructor() {
        try {
            new PathInfo("");
        } catch (PathInfoException $e) {
            $this->assertSame('Invalid path: Path is empty', $e->getMessage());
            $this->assertSame(error\InvalidPathError::class, $e->getCause()->getClass());
        }

        try {
            new PathInfo("null-byte-\0");
        } catch (PathInfoException $e) {
            $this->assertSame('Invalid path: Path contains NULL-bytes', $e->getMessage());
            $this->assertSame(error\InvalidPathError::class, $e->getCause()->getClass());
        }
    }

    function testStringCast() {
        $info = new PathInfo(__FILE__);

        $this->assertSame(__FILE__, (string) $info);
    }

    function testGetters() {
        $path = $this->util->fileMake();
        $info = new PathInfo($path);

        $this->assertSame($path, $info->getPath());
        $this->assertSame([
            'path' => $path,
            'realpath' => $path,
            'type' => filetype($path),
            'dirname' => dirname($path),
            'basename' => basename($path),
            'filename' => file_name($path),
            'extension' => file_extension($path),
        ], $info->getInfo());

        $dir = dirname($path);
        $this->assertSame($dir, $info->getDirectory());
        $this->assertSame(dirname($dir), $info->getRootDirectory());
        $this->assertSame($dir, $info->getParentDirectory());

        $this->assertEquals(new PathInfo($dir), $info->getDirectoryInfo());
        $this->assertEquals(new PathInfo($dir), $info->getDirInfo());
    }

    function testInfoGetters() {
        $info = new PathInfo(__FILE__);

        $this->assertSame('text/x-php', $info->getMime());
        $this->assertSame('file', $info->getType());
        $this->assertSame('php', $info->getExtension());
        $this->assertSame(dirname(__FILE__), $info->getDirname());
        $this->assertSame(basename(__FILE__), $info->getBasename());
        $this->assertSame(filename(__FILE__), $info->getFilename());
        $this->assertSame(__FILE__, $info->getRealPath());

        $this->assertNull($info->getLinkTarget());
        $this->assertIsInt($info->getLinkInfo());
    }

    function testStatMethods() {
        $info = new PathInfo(__FILE__);

        $this->assertIsArray($info->getStat());
        $this->assertIsInt($info->getStat()['size']);

        $info = new PathInfo('absent-file');

        $this->assertNull($info->getStat());
        $this->assertNull(@$info->getStat()['size']);

        $info = new PathInfo(__FILE__);

        $this->assertSame(filesize(__FILE__), $info->getSize());
        $this->assertSame(filectime(__FILE__), $info->getCTime());
        $this->assertSame(fileatime(__FILE__), $info->getATime());
        $this->assertSame(filemtime(__FILE__), $info->getMTime());
        $this->assertSame(fileinode(__FILE__), $info->getInode());
        $this->assertSame(filegroup(__FILE__), $info->getGroup());
        $this->assertSame(fileowner(__FILE__), $info->getOwner());
        $this->assertSame(fileperms(__FILE__), $info->getPerms());
    }

    function testCheckMethods() {
        $path = $this->util->file('test-file');
        $info = new PathInfo($path);

        $this->assertFalse($info->exists());

        touch($path);

        $this->assertTrue($info->exists());

        $this->assertFalse($info->isDirectory());
        $this->assertFalse($info->isDir());
        $this->assertTrue($info->isFile());
        $this->assertFalse($info->isLink());

        $this->assertTrue($info->isReadable());
        $this->assertTrue($info->isWritable());
        $this->assertFalse($info->isExecutable());
        $this->assertFalse($info->isHidden());

        $this->assertTrue($info->isAvailable());
        $this->assertTrue($info->isAvailableFor('read'));
        $this->assertTrue($info->isAvailableFor('write'));
        $this->assertFalse($info->isAvailableFor('execute'));

        unlink($path);
    }
}
