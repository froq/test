<?php declare(strict_types=1);
namespace test\froq\file;
use froq\file\{PathInfo, PathInfoException, error};

class PathInfoTest extends \TestCase
{
    function init() {
        $this->util = $this->util('file');
    }

    function testConstructor() {
        $path = __FILE__;
        $info = new PathInfo($path);

        $this->assertSame($path, $info->path);
        $this->assertSame(get_path_info($path), $info->info);

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

    function testMagicString() {
        $path = __FILE__;
        $info = new PathInfo($path);

        $this->assertSame($path, (string) $info);
    }

    function testMagicGet() {
        $path = __FILE__;
        $info = new PathInfo($path);

        $this->assertSame($path, $info->path);
        $this->assertSame('file', $info->type);
        $this->assertSame('php', $info->extension);

        foreach (['realPath', 'dirName', 'fileName', 'baseName'] as $key) {
            $func = $lkey = lower($key);
            $value = $func($path);
            $this->assertSame($value, $info->$key);
            $this->assertSame($value, $info->$lkey);
        }

        $this->expectException(PathInfoException::class);
        $info->absentInfoKey;
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
        $path = __FILE__;
        $info = new PathInfo($path);

        $this->assertSame('text/x-php', $info->getMime());
        $this->assertSame('file', $info->getType());
        $this->assertSame('php', $info->getExtension());
        $this->assertSame(dirname($path), $info->getDirname());
        $this->assertSame(basename($path), $info->getBasename());
        $this->assertSame(filename($path), $info->getFilename());
        $this->assertSame(realpath($path), $info->getRealPath());

        $this->assertNull($info->getLinkTarget());
        $this->assertIsInt($info->getLinkInfo());
    }

    function testStatMethods() {
        $path = __FILE__;
        $info = new PathInfo($path);

        $this->assertIsArray($info->getStat());
        $this->assertIsInt($info->getStat()['size']);

        $info = new PathInfo('absent-file');

        $this->assertNull($info->getStat());
        $this->assertNull(@$info->getStat()['size']);

        $info = new PathInfo($path);

        $this->assertSame(filesize($path), $info->getSize());
        $this->assertSame(filectime($path), $info->getCTime());
        $this->assertSame(fileatime($path), $info->getATime());
        $this->assertSame(filemtime($path), $info->getMTime());
        $this->assertSame(fileinode($path), $info->getInode());
        $this->assertSame(filegroup($path), $info->getGroup());
        $this->assertSame(fileowner($path), $info->getOwner());
        $this->assertSame(fileperms($path), $info->getPerms());

        $this->assertSame([
            'read' => true,
            'write' => true,
            'execute' => false
        ], $info->getPermsInfo());
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

    function testArrayAccess() {
        $path = __FILE__;
        $info = new PathInfo($path);

        $this->assertInstanceOf(\ArrayAccess::class, $info);

        $this->assertSame($path, $info['path']);
        $this->assertSame('file', $info['type']);
        $this->assertSame('php', $info['extension']);

        foreach (['realPath', 'dirName', 'fileName', 'baseName'] as $key) {
            $func = $lkey = lower($key);
            $value = $func($path);
            $this->assertSame($value, $info->$key);
            $this->assertSame($value, $info->$lkey);
        }

        $this->expectException(PathInfoException::class);
        $info['absent-info-key'];
    }
}
