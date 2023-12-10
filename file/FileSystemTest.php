<?php declare(strict_types=1);
namespace test\froq\file;
use froq\file\{FileSystem, FileSystemException,
    Stat, StatException, Path, PathException, PathInfo, PathInfoException,
    Directory, DirectoryException, File, FileException, error};

class FileSystemTest extends \TestCase
{
    function init() {
        $this->util = $this->util('file');
    }

    function testGetStat() {
        $this->assertInstanceOf(Stat::class, FileSystem::getStat(__DIR__));

        try {
            FileSystem::getStat("");
        } catch (FileSystemException $e) {
            $this->assertInstanceOf(StatException::class, $e->getCause());
            $this->assertInstanceOf(error\InvalidPathError::class, $e->getCause()->getCause());
        }
    }

    function testGetPath() {
        $this->assertInstanceOf(Path::class, FileSystem::getPath(__DIR__));

        try {
            FileSystem::getPath("");
        } catch (FileSystemException $e) {
            $this->assertInstanceOf(PathException::class, $e->getCause());
            $this->assertInstanceOf(error\InvalidPathError::class, $e->getCause()->getCause());
        }
    }

    function testGetPathInfo() {
        $this->assertInstanceOf(PathInfo::class, FileSystem::getPathInfo(__DIR__));

        try {
            FileSystem::getPathInfo("");
        } catch (FileSystemException $e) {
            $this->assertInstanceOf(PathInfoException::class, $e->getCause());
            $this->assertInstanceOf(error\InvalidPathError::class, $e->getCause()->getCause());
        }
    }

    function testOpenDirectory() {
        $this->assertInstanceOf(Directory::class, FileSystem::openDirectory(__DIR__));

        try {
            FileSystem::openDirectory("");
        } catch (FileSystemException $e) {
            $this->assertInstanceOf(DirectoryException::class, $e->getCause());
            $this->assertInstanceOf(error\InvalidPathError::class, $e->getCause()->getCause());
        }

        try {
            FileSystem::openDirectory("absent-file");
        } catch (FileSystemException $e) {
            $this->assertInstanceOf(DirectoryException::class, $e->getCause());
            $this->assertInstanceOf(error\NoFileError::class, $e->getCause()->getCause());
        }

        try {
            FileSystem::openDirectory(__FILE__);
        } catch (FileSystemException $e) {
            $this->assertInstanceOf(DirectoryException::class, $e->getCause());
            $this->assertInstanceOf(error\NotADirectoryError::class, $e->getCause()->getCause());
        }
    }

    function testOpenFile() {
        $this->assertInstanceOf(File::class, FileSystem::openFile(__FILE__));

        try {
            FileSystem::openFile("");
        } catch (FileSystemException $e) {
            $this->assertInstanceOf(FileException::class, $e->getCause());
            $this->assertInstanceOf(error\InvalidPathError::class, $e->getCause()->getCause());
        }

        try {
            FileSystem::openFile("absent-file");
        } catch (FileSystemException $e) {
            $this->assertInstanceOf(FileException::class, $e->getCause());
            $this->assertInstanceOf(error\NoFileError::class, $e->getCause()->getCause());
        }

        try {
            FileSystem::openFile(__DIR__);
        } catch (FileSystemException $e) {
            $this->assertInstanceOf(FileException::class, $e->getCause());
            $this->assertInstanceOf(error\NotAFileError::class, $e->getCause()->getCause());
        }
    }

    function testMakeDirectory() {
        $path = $this->util->dir();
        $this->assertSame($path, FileSystem::makeDirectory($path));

        $this->expectException(FileSystemException::class);
        $this->expectExceptionMessage('File exists');
        FileSystem::makeDirectory($path);
    }

    function testMakeFile() {
        $path = $this->util->file();
        $this->assertSame($path, FileSystem::makeFile($path));

        $this->expectException(FileSystemException::class);
        $this->expectExceptionMessage('File exists');
        FileSystem::makeFile($path);
    }

    function testRemoveDirectory() {
        $path = $this->util->dirMake();
        $this->assertTrue(FileSystem::removeDirectory($path));

        $this->expectException(FileSystemException::class);
        $this->expectExceptionMessage('No such file or directory');
        FileSystem::removeDirectory($path);
    }

    function testRemoveFile() {
        $path = $this->util->fileMake();
        $this->assertTrue(FileSystem::removeFile($path));

        $this->expectException(FileSystemException::class);
        $this->expectExceptionMessage('No such file or directory');
        FileSystem::removeFile($path);
    }

    function testReadFile() {
        $path = $this->util->fileMake();
        $this->assertIsString(FileSystem::readFile($path));

        $this->expectException(FileSystemException::class);
        $this->expectExceptionMessage('No such file');
        FileSystem::readFile('absent-file');
    }

    function testWriteFile() {
        $path = $this->util->fileMake();
        $this->assertIsInt(FileSystem::writeFile($path, 'abc'));
    }

    function testAppendFile() {
        $path = $this->util->fileMake();
        $this->assertIsInt(FileSystem::appendFile($path, 'abc'));
    }

    function testSplitPaths() {
        $path = join(DIRECTORY_SEPARATOR, $paths = ['a', 'b', 'c']);
        $this->assertSame($paths, FileSystem::splitPaths($path));
    }

    function testJoinPaths() {
        $paths = split(DIRECTORY_SEPARATOR, $path = join(DIRECTORY_SEPARATOR, ['a', 'b', 'c']));
        $this->assertSame($path, FileSystem::joinPaths(...$paths));
    }

    function testResolvePath() {
        $this->assertSame(getcwd(), FileSystem::resolvePath('.'));
        $this->assertNull(FileSystem::resolvePath('.' . DIRECTORY_SEPARATOR . 'absent-path'));
    }

    function testNormalizePath() {
        $this->assertSame(getcwd(), FileSystem::normalizePath('.'));
        $this->assertNotNull(FileSystem::normalizePath('.' . DIRECTORY_SEPARATOR . 'absent-path'));
    }
}
