<?php declare(strict_types=1);
namespace froq\test\file\object;
use froq\file\object\{FileObject, FileObjectException};

class FileObjectTest extends \TestCase
{
    function setUp(): void {
        $this->util = $this->util('File');
    }

    function test_constructor() {
        $fo = new FileObject($this->util->fileMake());

        $this->assertIsResource($fo->getResource());
        $this->assertFileExists($fo->getResourceFile());

        $fo->free(force: true); // Clean up.

        $this->assertIsNotResource($fo->getResource());
        $this->assertFileNotExists((string) $fo->getResourceFile());
    }

    function test_openClose() {
        $fo = new FileObject();
        $fo->open($this->util->fileMake());

        $this->assertIsResource($fo->getResource());
        $this->assertFileExists($fo->getResourceFile());

        $fo->close(); // Clean up.

        $this->expectException(FileObjectException::class);
        $this->expectExceptionMessage('No file given & no resource file to process');

        $fo->open();
    }

    function test_saveUnsave() {
        $fo = new FileObject($this->util->fileMake());
        $savedFile = $fo->save(directory: tmp());

        $this->assertFileExists($savedFile);
        $this->assertTrue($fo->unsave($savedFile));
        $this->assertFileNotExists($savedFile);
        $this->assertFalse($fo->unsave($savedFile));
    }

    function test_free() {
        $fo = new FileObject($this->util->fileMake());

        $this->assertTrue($fo->free());
        $this->assertFalse($fo->free());
        $this->assertTrue($fo->isFreed());
        $this->assertFalse($fo->isValid());
    }

    function test_writeRead() {
        $fo = new FileObject($this->util->fileMake());
        $contents = 'Hello!';

        $this->assertSame(6, $fo->write($contents));

        $fo->rewind();
        $this->assertSame($contents, $fo->read(6));
        $this->assertSame($contents, $fo->readAll());

        $fo->rewind();
        $this->assertSame($contents[0], $fo->readChar());
        $this->assertSame($contents[1], $fo->readChar());

        $fo->setPosition(strlen($contents)); // Reset position.

        $fo->write(PHP_EOL . $contents);
        $fo->rewind();

        $this->assertSame($contents, $fo->readLine());
        $this->assertSame($contents, $fo->readUntil(PHP_EOL));

        $fo->empty();

        $this->assertSame('', $fo->readAll());
    }

    function test_offset() {
        $fo = new FileObject($this->util->fileMake());
        $this->assertSame(0, $fo->offset());

        $fo->write('a');
        $this->assertSame(1, $fo->offset());
        $this->assertTrue($fo->offset(0));
    }

    function test_valid() {
        $fo = new FileObject($this->util->fileMake());
        $this->assertTrue($fo->valid());

        $fo->read(1); // EOF.
        $this->assertFalse($fo->valid());
    }

    function test_stat() {
        $fo = new FileObject($this->util->fileMake());
        $this->assertSame(0, $fo->stat()['size']);

        $fo->write('a');
        $this->assertSame(1, $fo->stat()['size']);
    }

    function test_meta() {
        $fo = new FileObject($file = $this->util->fileMake());
        $this->assertSame($file, $fo->meta()['uri']);
    }

    function test_info() {
        $fo = new FileObject($file = $this->util->fileMake());
        $this->assertSame(0, $fo->info()['size']);
        $this->assertSame($file, $fo->info()['meta']['uri']);
    }

    function test_pathInfo() {
        $fo = new FileObject($file = $this->util->fileMake());
        $info = $fo->pathInfo();

        $this->assertSame($file, $info['path']);
        $this->assertSame($file, $info['realpath']);
        $this->assertSame('file', $info['type']);
        $this->assertSame(dirname($file), $info['dirname']);
        $this->assertSame(basename($file), $info['basename']);
        $this->assertSame(filename($file), $info['filename']);
        $this->assertSame(null, $info['extension']);

        $this->assertSame($file, $fo->path());
        $this->assertSame(filename($file), $fo->name());
        $this->assertSame(dirname($file), $fo->directory());
        $this->assertSame(null, $fo->extension());
    }

    function test_contents() {
        $fo = new FileObject($this->util->fileMake());
        $this->assertSame('Hello!', $fo->setContents('Hello!')->getContents());
    }

    function test_position() {
        $fo = new FileObject($this->util->fileMake());
        $this->assertTrue($fo->setPosition(1));
        $this->assertSame(1, $fo->getPosition());
    }

    function test_isEnded() {
        $fo = new FileObject($this->util->fileMake());
        $this->assertFalse($fo->isEnded());

        $fo->read(1);
        $this->assertTrue($fo->isEnded());
    }

    function test_isEmpty() {
        $fo = new FileObject($this->util->fileMake());
        $this->assertTrue($fo->isEmpty());

        $fo->write('a');
        $this->assertFalse($fo->isEmpty());
    }

    function test_size() {
        $fo = new FileObject($this->util->fileMake());
        $this->assertSame(0, $fo->size());

        $fo->write('a');
        $this->assertSame(1, $fo->size());
    }

    function test_toString() {
        $fo = new FileObject($this->util->fileMake());
        $this->assertSame('', $fo->toString());

        $fo->write('a');
        $this->assertSame('a', $fo->toString());
    }

    function test_fromFile() {
        $fo = FileObject::fromFile($this->util->fileMake());
        $this->assertInstanceOf(FileObject::class, $fo);

        $this->expectException(FileObjectException::class);
        $this->expectExceptionMessage('No file exists [file: absent-file]');
        FileObject::fromFile('absent-file');
    }

    function test_fromString() {
        $fo = FileObject::fromString('Hello!');
        $this->assertInstanceOf(FileObject::class, $fo);
        $this->assertSame('Hello!', $fo->toString());
    }
}
