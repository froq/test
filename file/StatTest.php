<?php declare(strict_types=1);
namespace test\froq\file;
use froq\file\{Stat, StatException, error};

class StatTest extends \TestCase
{
    function testConstructor() {
        $path = __FILE__;
        $stat = new Stat($path);

        $this->assertSame($path, $stat->path);
        $this->assertSame(stat($path), $stat->info);

        try {
            new Stat("");
        } catch (StatException $e) {
            $this->assertSame('Invalid path: Path is empty', $e->getMessage());
            $this->assertSame(error\InvalidPathError::class, $e->getCause()->getClass());
        }

        try {
            new Stat("null-byte-\0");
        } catch (StatException $e) {
            $this->assertSame('Invalid path: Path contains NULL-bytes', $e->getMessage());
            $this->assertSame(error\InvalidPathError::class, $e->getCause()->getClass());
        }

        try {
            new Stat("absent-file");
        } catch (StatException $e) {
            $this->assertSame('Failed to open stat: No such file or directory', $e->getMessage());
            $this->assertSame(error\NoFileError::class, $e->getCause()->getClass());
        }
    }

    function testGetters() {
        $path = __FILE__;
        $stat = new Stat($path);

        $this->assertSame($path, $stat->getPath());
        $this->assertSame(stat($path), $stat->getInfo());
    }

    function testStatMethods() {
        $path = __FILE__;
        $stat = new Stat($path);

        $this->assertSame(filesize($path), $stat->getSize());
        $this->assertSame(filectime($path), $stat->getCTime());
        $this->assertSame(fileatime($path), $stat->getATime());
        $this->assertSame(filemtime($path), $stat->getMTime());
        $this->assertSame(fileinode($path), $stat->getInode());
        $this->assertSame(filegroup($path), $stat->getGroup());
        $this->assertSame(fileowner($path), $stat->getOwner());
        $this->assertSame(fileperms($path), $stat->getPerms());
    }

    function testCheckMethods() {
        $path = __FILE__;
        $stat = new Stat($path);

        $this->assertFalse($stat->isDirectory());
        $this->assertFalse($stat->isDir());
        $this->assertTrue($stat->isFile());
        $this->assertFalse($stat->isLink());

        $this->assertTrue($stat->isReadable());
        $this->assertTrue($stat->isWritable());
        $this->assertFalse($stat->isExecutable());
    }



    function testArrayAccess() {
        $path = __FILE__;
        $stat = new Stat($path);

        $this->assertInstanceOf(\ArrayAccess::class, $stat);

        $this->assertSame(filesize($path), $stat['size']);
        $this->assertSame(filectime($path), $stat['ctime']);
        $this->assertSame(fileatime($path), $stat['atime']);
        $this->assertSame(filemtime($path), $stat['mtime']);
        $this->assertSame(fileinode($path), $stat['ino']);
        $this->assertSame(filegroup($path), $stat['gid']);
        $this->assertSame(fileowner($path), $stat['uid']);
        $this->assertSame(fileperms($path), $stat['mode']);
        $this->assertSame(-1, $stat['invalid-field']);
    }
}
