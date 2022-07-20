<?php declare(strict_types=1);
namespace test\froq\file;
use froq\file\{File, FileError, FileException};
use froq\file\object\FileObject;
use froq\util\misc\System;

class FileTest extends \TestCase
{
    function setUp(): void {
        $this->util = $this->util('file');
    }

    function test_getMime() {
        $this->assertSame('text/x-php', File::getMime(__file__));
        $this->assertSame('directory', File::getMime(__dir__));
        $this->assertSame(null, File::getMime('absent-file'));
    }

    function test_getExtension() {
        $this->assertSame('php', File::getExtension(__file__));
        $this->assertSame(null, File::getExtension('maybe-exists-file'));
        $this->assertSame('txt', File::getExtension('maybe-exists-file.txt'));
    }

    function test_isFile() {
        $this->assertTrue(File::isFile(__file__));
        $this->assertFalse(File::isFile(__dir__));
        $this->assertFalse(File::isFile("null-\0-byte-filename"));
    }

    function test_isDirectory() {
        $this->assertTrue(File::isDirectory(__dir__));
        $this->assertFalse(File::isDirectory(__file__));
        $this->assertFalse(File::isDirectory("null-\0-byte-dirname"));
    }

    function test_isAvailable() {
        $file = $this->util->fileMake();

        $this->assertTrue(File::isReadable($file));
        $this->assertTrue(File::isWritable($file));
        $this->assertTrue(File::isAvailable($file));
    }

    function test_make() {
        $dir = tmp(); // @sugar
        $file = $dir . '/test-file' . suid(); // @sugar
        $prefix = 'test-file-prefix';

        $this->assertNotNull(File::make($file));
        $this->assertNotNull($tempFile = File::make($prefix, temp: true));

        $this->util->drop($file, $tempFile);

        $nullFile = null;
        try {
            $nullFile = File::make($dir);
        } catch (FileException $e) {
            $this->assertNull($nullFile);
            $this->assertMatches(sprintf(
                "~Cannot make file %s, it's a directory~", $dir
            ), $e->getMessage());
        }
    }

    function test_open() {
        $files = [
            $this->util->fileMake('test-0'),
            $this->util->fileMake('test-1'),
        ];

        $this->assertInstanceOf(FileObject::class, File::open($files[0]));

        $invalidFile = 'invalid-or-absent-file';
        try {
            File::open($invalidFile);
        } catch (FileException $e) {
            $this->assertStringContains("No file exists [file: {$invalidFile}]", $e->getMessage());
        }

        $invalidMode = 'z';
        try {
            File::open($files[1], mode: $invalidMode);
        } catch (FileException $e) {
            $this->assertStringContains("{$invalidMode}' is not a valid mode", $e->getMessage());
        }
    }

    function test_openTemp() {
        $this->assertInstanceOf(FileObject::class, File::openTemp());

        $invalidMode = 'z';
        try {
            File::openTemp('', mode: $invalidMode);
        } catch (FileException $e) {
            $this->assertStringContains("{$invalidMode}' is not a valid mode", $e->getMessage());
        }
    }

    function test_getContents() {
        $file = $this->util->fileMake();
        $resouce = fopen($file, 'r');

        $this->assertEmpty(File::getContents($file));
        $this->assertEmpty(File::getContents($resouce));

        fclose($resouce);
    }

    function test_setContents() {
        $file = $this->util->fileMake();
        $resouce = fopen($file, 'r+b');
        $contents = 'Hello!';

        $this->assertNotEmpty(File::setContents($file, $contents));
        $this->assertNotEmpty(File::setContents($resouce, $contents));
        $this->assertStringEqualsFile($file, $contents);

        fclose($resouce);
    }

    function test_errorCheck() {
        File::errorCheck("", $e);
        $this->assertSame(FileError::NO_VALID_PATH, $e->getCode());
        $this->assertSame('No valid path, path is empty', $e->getMessage());

        File::errorCheck("null-\0-byte-filename", $e);
        $this->assertSame(FileError::NO_VALID_PATH, $e->getCode());
        $this->assertSame('No valid path, path contains NULL-bytes', $e->getMessage());

        File::errorCheck(__dir__, $e);
        $this->assertSame(FileError::DIRECTORY, $e->getCode());
        $this->assertStringStartsWith('Given path is a directory', $e->getMessage());

        File::errorCheck("absent-file", $e);
        $this->assertSame(FileError::NO_FILE_EXISTS, $e->getCode());
        $this->assertStringStartsWith('No file exists', $e->getMessage());

        File::errorCheck("absent-file", $e);
        $this->assertSame(FileError::NO_FILE_EXISTS, $e->getCode());
        $this->assertStringStartsWith('No file exists', $e->getMessage());

        // Unix only.
        if (System::isUnix() && File::errorCheck("/root", $e)) {
            $this->assertSame(FileError::NO_ACCESS_PERMISSION, $e->getCode());
            $this->assertStringStartsWith('No access permission', $e->getMessage());
        }
    }
}
