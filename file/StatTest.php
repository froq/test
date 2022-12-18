<?php declare(strict_types=1);
namespace test\froq\file;
use froq\file\{Stat, StatException, error};

class StatTest extends \TestCase
{
    function testConstructor() {
        try {
            new Stat("null-byte-\0");
        } catch (StatException $e) {
            $this->assertSame('Invalid path: Path contains NULL-bytes', $e->getMessage());
            $this->assertSame(error\InvalidPathError::class, $e->getCause()->getClass());
        }

        try {
            new Stat("");
        } catch (StatException $e) {
            $this->assertSame('Invalid path: Path is empty', $e->getMessage());
            $this->assertSame(error\InvalidPathError::class, $e->getCause()->getClass());
        }
    }

    function testGetters() {
        $stat = new Stat(__FILE__);

        $this->assertSame(__FILE__, $stat->getPath());
        $this->assertSame(stat(__FILE__), $stat->getInfo());
    }

    function testStatMethods() {
        $stat = new Stat(__FILE__);

        $this->assertSame(filesize(__FILE__), $stat->getSize());
        $this->assertSame(filectime(__FILE__), $stat->getCTime());
        $this->assertSame(fileatime(__FILE__), $stat->getATime());
        $this->assertSame(filemtime(__FILE__), $stat->getMTime());
        $this->assertSame(fileinode(__FILE__), $stat->getInode());
        $this->assertSame(filegroup(__FILE__), $stat->getGroup());
        $this->assertSame(fileowner(__FILE__), $stat->getOwner());
        $this->assertSame(fileperms(__FILE__), $stat->getPerms());
    }

    function testCheckMethods() {
        $stat = new Stat(__FILE__);

        $this->assertFalse($stat->isDirectory());
        $this->assertFalse($stat->isDir());
        $this->assertTrue($stat->isFile());
        $this->assertFalse($stat->isLink());

        $this->assertTrue($stat->isReadable());
        $this->assertTrue($stat->isWritable());
        $this->assertFalse($stat->isExecutable());
    }
}
