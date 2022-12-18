<?php declare(strict_types=1);
namespace test\froq\file;
use froq\file\Stream;

class StreamTest extends \TestCase
{
    function testConstructor() {
        $stream = new Stream($fp = fopen(__FILE__, 'r'));

        $this->assertTrue(is_resource($fp));
        $this->assertSame($fp, $stream->resource());

        $this->expectException(\ArgumentError::class);
        $this->expectExceptionMessage('Argument $resource must be a stream, null given');
        new Stream(null);
    }

    function testDestructor() {
        $stream = new Stream($fp = fopen(__FILE__, 'r'));

        $stream = null;
        $this->assertFalse(is_resource($fp));

        $tempfile = tmpnam();
        $this->assertFileExists($tempfile);

        $stream = new Stream(fopen($tempfile, 'r'), $tempfile);
        $this->assertSame($tempfile, $stream->tempfile());

        $stream = null;
        $this->assertFileNotExists($tempfile);
    }

    function testGetters() {
        $stream = new Stream(fopen(__FILE__, 'r'));

        $this->assertIsResource($stream->resource());
        $this->assertIsInt($stream->id());
        $this->assertIsArray($stream->meta());
        $this->assertSame('stdio', $stream->type());

        $this->assertNull($stream->tempfile());

        $stream = new Stream(fopen($tempfile = tmpnam(), 'r'), $tempfile);
        $this->assertSame($tempfile, $stream->tempfile());
    }

    function testCloseValid() {
        $stream = new Stream(fopen(__FILE__, 'r'));

        $this->assertTrue($stream->valid());

        $this->assertTrue($stream->close());
        $this->assertFalse($stream->valid());

        $this->assertFalse($stream->close());
        $this->assertFalse($stream->valid());
    }
}
