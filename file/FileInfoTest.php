<?php declare(strict_types=1);
namespace test\froq\file;
use froq\file\{FileInfo, FileInfoException};
use froq\file\system\{Path, File, Directory};

class FileInfoTest extends \TestCase
{
    function setUp(): void {
        $this->util = $this->util('file');
    }

    function test_constructor() {
        try {
            new FileInfo("null-byte-filename-\0");
        } catch (FileInfoException $e) {
            $this->assertSame('Invalid path, path contains NULL-bytes', $e->getMessage());
        }

        try {
            new FileInfo("");
        } catch (FileInfoException $e) {
            $this->assertSame('Invalid path, empty path given', $e->getMessage());
        }
    }

    function test_getters() {
        $info = new FileInfo(__file__);

        $this->assertSame('text/x-php', $info->getMime());
        $this->assertSame('file', $info->getType());
        $this->assertSame('php', $info->getExtension());
        $this->assertSame(dirname(__file__), $info->getDirname());
        $this->assertSame(filename(__file__), $info->getFilename());

        $this->assertInstanceOf(FileInfo::class, $info->getDirInfo());
        $this->assertInstanceOf(FileInfo::class, $info->getDirectoryInfo()); // Alias.
    }

    function test_checkers() {
        $file = $this->util->file('test-file');
        $info = new FileInfo($file);

        $this->assertFalse($info->exists());

        touch($file);

        $this->assertTrue($info->exists());
        $this->assertTrue($info->isAvailable());
        $this->assertTrue($info->isAvailableFor('read'));
        $this->assertTrue($info->isAvailableFor('write'));
        $this->assertFalse($info->isAvailableFor('execute'));

        unlink($file);
    }

    function test_stats() {
        $info = new FileInfo(__file__);

        $this->assertIsArray($info->getStats());
        $this->assertIsInt($info->getStat('size'));

        $info = new FileInfo('absent-file');

        $this->assertNull($info->getStats());
        $this->assertFalse($info->getStat('size'));
    }
}
