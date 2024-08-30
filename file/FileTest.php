<?php declare(strict_types=1);
namespace test\froq\file;
use froq\file\{File, FileException, Path, PathInfo, PathObject, Directory, Stat,
    error, upload};

class FileTest extends \TestCase
{
    function init() {
        $this->util = $this->util('file');
    }

    function testConstructor() {
        $file = new File($path = $this->util->fileMake());

        $this->assertInstanceOf(PathObject::class, $file);
        $this->assertInstanceOf(Path::class, $file->path);
        $this->assertSame($path, $file->getPathName());

        // Argument types (string|Path).
        $this->assertEquals(new File($path), new File(new Path($path)));

        // Temporary files.
        $file = new File('', ['temp' => true, 'tempDrop' => true]);

        $this->assertFileExists($file->getPathName());
        $this->assertTrue($file->close()); // Delete temp file.
        $this->assertFileNotExists($file->getPathName());

        try {
            new File("");
        } catch (FileException $e) {
            $this->assertSame('Invalid path: Path is empty', $e->getMessage());
            $this->assertSame(error\InvalidPathError::class, $e->getCause()->getClass());
        }

        try {
            new File("null-byte-\0");
        } catch (FileException $e) {
            $this->assertSame('Invalid path: Path contains NULL-bytes', $e->getMessage());
            $this->assertSame(error\InvalidPathError::class, $e->getCause()->getClass());
        }
    }

    function testSettersGetters() {
        $file = new File($this->util->imageMake());

        $this->assertNull($file->getLine());
        $this->assertSame([1, null], $file->setLine(1)->getLine());

        $this->assertSame('image/png', $file->getMime());
        $this->assertSame('image/jpeg', $file->setMime('image/jpeg')->getMime());

        $this->assertSame('png', $file->getExtension());
        $this->assertSame('jpeg', $file->setExtension('jpeg')->getExtension());
    }

    function testOpenCloseValid() {
        $file = new File($this->util->fileMake());

        $this->assertSame($file, $file->open());
        $this->assertTrue($file->valid());
        $this->assertTrue($file->close());

        $this->assertFalse($file->valid());
        $this->assertFalse($file->close());

        try {
            new File('absent-file', ['open' => 'r']);
        } catch (FileException $e) {
            $this->assertSame('Failed to open stream: No such file or directory', $e->getMessage());
            $this->assertSame(error\NoFileError::class, $e->getCause()->getClass());
        }

        try {
            new File(__FILE__, ['open' => 'z']);
        } catch (FileException $e) {
            $this->assertSame("Failed to open stream: 'z' is not a valid mode", $e->getMessage());
        }

        try {
            new File(__DIR__);
        } catch (FileException $e) {
            $this->assertSame('Cannot use a directory as a file', $e->getMessage());
            $this->assertSame(error\NotAFileError::class, $e->getCause()->getClass());
        }
    }

    function testWriteRead() {
        $file = new File($this->util->fileMake(), ['open' => 'w+']);

        $this->assertSame(3, $file->write('abc'));
        $this->assertSame(3, $file->writeAll('abc'));
        $this->assertSame(4, $file->writeLine('abc', PHP_EOL));

        $file->rewind();
        $this->assertSame('a', $file->read(1));
        $this->assertSame('bc', $file->readLine());
        $this->assertSame('abc' . PHP_EOL, $file->readAll());

        $file->rewind();
        $this->assertSame('a', $file->readChar());
        $this->assertSame('b', $file->readUntil('c'));

        $this->expectException(FileException::class);
        $this->expectExceptionMessageMatches('~Bad file descriptor~');
        // Invalid mode 'r' for write.
        $file = new File($this->util->fileMake(), ['open' => 'r']);
        $file->write('abc');
    }

    function testEmpty() {
        $file = new File($this->util->fileMake(), ['open' => 'w+']);

        $file->write('abc');
        $this->assertSame(3, $file->size());
        $this->assertTrue($file->empty());
        $this->assertSame(0, $file->size());
    }

    function testTellSeekRewind() {
        $file = new File($this->util->fileMake(), ['open' => 'w+']);

        $this->assertSame(0, $file->tell());
        $this->assertTrue($file->seek(1));
        $this->assertTrue($file->rewind());

        $file->write('abc');
        $this->assertSame(3, $file->tell());
    }

    function testLockUnlock() {
        $file = new File($this->util->fileMake(), ['open' => 'w+']);

        $this->assertTrue($file->lock());
        $this->assertTrue($file->unlock());
    }

    function testMetaStat() {
        $file = new File($this->util->fileMake(), ['open' => 'r']);

        $this->assertIsArray($file->meta());
        $this->assertIsArray($file->stat());
    }

    function testSize() {
        $file = new File($this->util->fileMake(), ['open' => 'a']);

        $this->assertSame(0, $file->size());

        $file->write('abc');
        $this->assertSame(3, $file->size());
    }

    function testSetGetContents() {
        $file = new File($this->util->fileMake(), ['open' => 'a+']);

        $this->assertSame('', $file->getContents());
        $this->assertSame(3, $file->setContents('abc'));
    }

    function testEof() {
        $file = new File($this->util->fileMake());
        $file->open('r+')->write('abc');

        $this->assertFalse($file->eof());

        while (!$file->eof()) {
            $file->readChar();
        }

        $this->assertTrue($file->eof());
    }

    function testCopy() {
        $file = new File($this->util->fileMake(), ['open' => 'w']);

        $from = $this->util->fileMake();
        $this->assertSame($file, $file->copy($from));

        $this->expectException(FileException::class);
        $this->expectExceptionMessageMatches('~No such file~');
        $file->copy('absent-file');
    }

    function testSave() {
        $file = new File($this->util->fileMake(), ['open' => 'r']);

        $to = $this->util->fileMake();
        $this->assertSame($to, $file->save($to, force: true));

        $this->expectException(FileException::class);
        $this->expectExceptionMessageMatches('~Cannot overwrite existing file~');
        $file->save($to, force: false);
    }

    function testMove() {
        $file = new File($this->util->fileMake(), ['open' => 'r']);

        $to = $this->util->fileMake();
        $this->assertSame($to, $file->move($to, force: true));

        $this->expectException(FileException::class);
        $this->expectExceptionMessageMatches('~Cannot overwrite existing file~');
        $file->move($to, force: false);
    }

    function testDelete() {
        $file1 = new File($this->util->fileMake());
        $file2 = new File('absent-file');

        $this->assertTrue($file1->delete());
        $this->assertFalse($file2->delete());
    }

    function testConverters() {
        $file = new File($this->util->fileMake());
        $file->open('r+')->write('abc');

        $this->assertSame('abc', $file->toString());
        $this->assertSame('YWJj', $file->toBase64());
        $this->assertSame('data:text/plain;base64,YWJj', $file->toDataUrl());
    }

    function testCount() {
        $file = new File($this->util->fileMake());
        $file->open('r+');

        foreach (range(1, 3) as $i) {
            $line = 'abc' . rand();
            $file->writeLine($line);
        }

        $this->assertInstanceOf(\Countable::class, $file);
        $this->assertCount(3, $file);
        $this->assertSame(3, $file->count());
    }

    function testGetIterator() {
        $file = new File($this->util->fileMake());
        $file->open('r+');

        $lines = [];
        foreach (range(1, 3) as $i) {
            $lines[$i] = $line = 'abc' . rand();
            $file->writeLine($line);
        }

        foreach ($file as $i => $line) {
            $this->assertSame($lines[$i], $line);
        }

        $this->assertInstanceOf(\IteratorAggregate::class, $file);
        $this->assertInstanceOf(\Generator::class, $file->getIterator());
    }

    function testGetDirectory() {
        $path = $this->util->fileMake();
        $file = new File($path);

        $this->assertEquals(new Directory(dirname($path)), $file->getDirectory());
    }

    function testToSource() {
        $file = new File($this->util->fileMake());

        $this->assertInstanceOf(upload\FileSource::class, $file->toSource());
        $this->assertInstanceOf(upload\FileSource::class, $file->toFileSource());
    }

    function testFromString() {
        $file = File::fromString('abc');

        $this->assertInstanceOf(File::class, $file);
    }

    function testFromFileString() {
        $file = File::fromFileString($this->util->fileMake());

        $this->assertInstanceOf(File::class, $file);

        $this->expectException(FileException::class);
        $this->expectExceptionMessage('No such file');
        $file = File::fromFileString('absent-file');
    }

    /** Inherit Methods */

    function testGetPath() {
        $path = __FILE__;
        $file = new File($path);

        $this->assertSame($path, $file->getPathName());
        $this->assertSame($path, $file->getPath()->getName());
        $this->assertEquals(new Path($path), $file->getPath());
    }

    function testGetPathInfo() {
        $path = __FILE__;
        $file = new File($path);

        $this->assertEquals(new PathInfo($path), $file->getPathInfo());
    }

    function testGetStat() {
        $path = __FILE__;
        $file = new File($path);

        $this->assertEquals(new Stat($path), $file->getStat());

        $file = new File('absent-file');

        $this->assertNull($file->getStat());
    }

    function testExists() {
        $path = $this->util->fileMake();
        $file = new File($path);

        $this->assertTrue($file->exists());

        unlink($path);

        $this->assertFalse($file->exists());
    }

    function testOkay() {
        $path = $this->util->fileMake();
        $file = new File($path);

        $this->assertTrue($file->okay(read: true));
        $this->assertTrue($file->okay(write: true));
        $this->assertTrue($file->okay(execute: true));

        chmod($path, 0);

        $this->assertFalse($file->okay(read: true));
        $this->assertFalse($file->okay(write: true));
        $this->assertFalse($file->okay(execute: true));
    }

    function testPermMethods() {
        $path = $this->util->fileMake();
        $file = new File($path);

        $this->assertTrue($file->isReadable());
        $this->assertTrue($file->isWritable());
        $this->assertTrue($file->isExecutable());

        chmod($path, 0);

        $this->assertFalse($file->isReadable());
        $this->assertFalse($file->isWritable());
        $this->assertFalse($file->isExecutable());
    }

    function testCheckMethods() {
        $file = new File($this->util->fileMake());

        $this->assertTrue($file->isTemp());
        $this->assertFalse($file->isHidden());

        $file = new File('/.test');

        $this->assertFalse($file->isTemp());
        $this->assertTrue($file->isHidden());
    }

    function testModeTouch() {
        $file = new File($this->util->fileMake());

        $this->assertTrue($file->mode(0777));
        $this->assertTrue($file->touch(time()));
    }

    function testLinkUnlink() {
        $path = $this->util->fileMake();
        $file = new File($path);
        $link = $path . 'link';

        $this->assertTrue($file->link($link));

        $link = new File($link);

        $this->assertTrue($link->unlink());

        $this->expectException(FileException::class);
        $this->expectExceptionMessageMatches('~Cannot unlink a file~');
        $file->unlink();
    }

    function testRename() {
        $path = $this->util->fileMake();
        $file = new File($path);
        $to = $path . 'to';

        $this->assertTrue($file->rename($to, force: true));

        $this->expectException(FileException::class);
        $this->expectExceptionMessageMatches('~Cannot rename path~');
        $file->rename($to, force: false);
    }

    function testRemove() {
        $path = $this->util->fileMake();
        $file = new File($path);

        $this->assertTrue($file->remove(force: true));

        $this->expectException(FileException::class);
        $this->expectExceptionMessageMatches('~Cannot remove path~');
        $file->remove(force: false);
    }

    function testCreate() {
        $file = new File($this->util->file());

        $this->assertTrue($file->create());

        $this->expectException(FileException::class);
        $this->expectExceptionMessageMatches('~File exists~');
        $file->create();
    }

    function testClear() {
        $file = new File($this->util->file());
        $file->open('a')->write('abc');

        $this->assertSame(3, $file->size());
        $this->assertTrue($file->clear(force: true));
        $this->assertSame(0, $file->size());
    }

    function testDrop() {
        $file = new File($this->util->fileMake());

        $this->assertTrue($file->drop(force: true));

        $this->expectException(FileException::class);
        $this->expectExceptionMessageMatches('~Cannot drop path~');
        $file->drop(force: false);
    }
}
