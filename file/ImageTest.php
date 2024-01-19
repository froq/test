<?php declare(strict_types=1);
namespace test\froq\file;
use froq\file\{Image, ImageException, File, Path, PathObject,
    error, upload};

class ImageTest extends \TestCase
{
    function init() {
        $this->util = $this->util('file');
    }

    function testConstructor() {
        $image = new Image($path = $this->util->imageMake());

        $this->assertInstanceOf(File::class, $image);

        $this->assertInstanceOf(PathObject::class, $image);
        $this->assertInstanceOf(Path::class, $image->path);
        $this->assertSame($path, $image->path->name);

        // Argument types (string|Path).
        $this->assertEquals(new Image($path), new Image(new Path($path)));

        try {
            new Image("");
        } catch (ImageException $e) {
            $this->assertSame('Invalid path: Path is empty', $e->getMessage());
            $this->assertSame(error\InvalidPathError::class, $e->getCause()->getClass());
        }

        try {
            new Image("null-byte-\0");
        } catch (ImageException $e) {
            $this->assertSame('Invalid path: Path contains NULL-bytes', $e->getMessage());
            $this->assertSame(error\InvalidPathError::class, $e->getCause()->getClass());
        }
    }

    function testInfo() {
        $image = new Image($this->util->imageMake());

        $this->assertSame([
            0 => 200, 1 => 200, 2 => 3,
            3 => 'width="200" height="200"',
            'bits' => 8, 'mime' => 'image/png',
            'type' => 3, 'width' => 200, 'height' => 200,
        ], $image->info());
    }

    function testDims() {
        $image = new Image($this->util->imageMake());

        $this->assertSame([
            0 => 200, 1 => 200
        ], $image->dims());
    }

    function testToSource() {
        $image = new Image($this->util->fileMake());

        $this->assertInstanceOf(upload\ImageSource::class, $image->toSource());
        $this->assertInstanceOf(upload\ImageSource::class, $image->toImageSource());
    }

    function testFromString() {
        $image = Image::fromString(file_read($this->util->imageMake()));

        $this->assertInstanceOf(Image::class, $image);

        $this->expectException(ImageException::class);
        $this->expectExceptionMessage('Data is not in a recognized format');
        $image = Image::fromString('abc');
    }

    function testFromFileString() {
        $image = Image::fromFileString($this->util->imageMake());

        $this->assertInstanceOf(Image::class, $image);

        $this->expectException(ImageException::class);
        $this->expectExceptionMessage('No such file');
        $image = Image::fromFileString('absent-file');
    }
}
