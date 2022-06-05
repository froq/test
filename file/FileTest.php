<?php declare(strict_types=1);
namespace froq\test\file;
use froq\file\{File, FileError, FileException};
use froq\file\object\FileObject;

class FileTest extends \TestCase
{
    function test_getMime() {
        $this->assertSame('text/x-php', File::getMime(__file__));
        $this->assertSame('directory', File::getMime(__dir__));
        $this->assertSame(null, File::getMime('absent-file'));
    }

    function test_getExtension() {
        $this->assertSame('php', File::getExtension(__file__));
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
        $file = tmpnam('test-file'); // @sugar

        $this->assertTrue(File::isReadable($file));
        $this->assertTrue(File::isWritable($file));
        $this->assertTrue(File::isAvailable($file));

        $this->drop($file);
    }

    function test_make() {
        $dir = tmp(); // @sugar
        $file = $dir . '/test-file' . suid(); // @sugar
        $prefix = 'test-file-prefix';

        $this->assertNotNull(File::make($file));
        $this->assertNotNull($tempFile = File::make($prefix, temp: true));

        $this->drop($file, $tempFile);

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
            tmpnam('test-file-0'), // @sugar
            tmpnam('test-file-1'), // @sugar
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
            File::open($files[1], $invalidMode);
        } catch (FileException $e) {
            $this->assertStringContains("{$invalidMode}' is not a valid mode", $e->getMessage());
        }

        $this->drop(...$files);
    }

    function test_openTemp() {
        $this->assertInstanceOf(FileObject::class, File::openTemp());

        $invalidMode = 'z';
        try {
            File::openTemp($invalidMode);
        } catch (FileException $e) {
            $this->assertStringContains("{$invalidMode}' is not a valid mode", $e->getMessage());
        }
    }

    function test_getContents() {
        $file = tmpnam('test-file'); // @sugar
        $filePointer = fopen($file, 'r');

        $this->assertEmpty(File::getContents($file));
        $this->assertEmpty(File::getContents($filePointer));

        fclose($filePointer);
        $this->drop($file);
    }

    function test_setContents() {
        $file = tmpnam('test-file'); // @sugar
        $filePointer = fopen($file, 'r+b');
        $contents = 'Hello!';

        $this->assertNotEmpty(File::setContents($file, $contents));
        $this->assertNotEmpty(File::setContents($filePointer, $contents));
        $this->assertStringEqualsFile($file, $contents);

        fclose($filePointer);
        $this->drop($file);
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

        // Linux only.
        if (DIRECTORY_SEPARATOR != "\\") {
            File::errorCheck("/root", $e);
            $this->assertSame(FileError::NO_ACCESS_PERMISSION, $e->getCode());
            $this->assertStringStartsWith('No access permission', $e->getMessage());
        }
    }

    private function drop(...$files) {
        foreach ($files as $file) {
            @unlink($file);
        }
    }
}
