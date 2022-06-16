<?php declare(strict_types=1);
namespace test\froq\file\object;
use froq\file\object\{ImageObject, ImageObjectException};

class ImageObjectTest extends \TestCase
{
    function setUp(): void {
        $this->util = $this->util('File');
    }

    function test_constructor() {
        $fo = new ImageObject($this->util->imageMake());

        $this->assertInstanceOf(\GdImage::class, $fo->getResource());
        $this->assertFileExists($fo->getResourceFile());

        $fo->free(force: true); // Clean up.

        $this->assertNotInstanceOf(\GdImage::class, $fo->getResource());
        $this->assertFileNotExists((string) $fo->getResourceFile());
    }

    function test_openClose() {
        $fo = new ImageObject();
        $fo->open($this->util->imageMake());

        $this->assertInstanceOf(\GdImage::class, $fo->getResource());
        $this->assertFileExists($fo->getResourceFile());

        $fo->close(); // Clean up.

        $this->expectException(ImageObjectException::class);
        $this->expectExceptionMessage('No file given & no resource file to process');

        $fo->open();
    }

    function test_saveUnsave() {
        $fo = new ImageObject($this->util->imageMake());
        $savedFile = $fo->save(directory: tmp());

        $this->assertFileExists($savedFile);
        $this->assertTrue($fo->unsave($savedFile));
        $this->assertFileNotExists($savedFile);
        $this->assertFalse($fo->unsave($savedFile));
    }

    function test_free() {
        $fo = new ImageObject($this->util->imageMake());

        $this->assertTrue($fo->free());
        $this->assertFalse($fo->free());
        $this->assertTrue($fo->isFreed());
        $this->assertFalse($fo->isValid());
    }

    function test_resize() {
        $fo = new ImageObject($this->util->imageMake());
        $this->assertSame([100, 100], $fo->resize(100, 0)->dimensions());
    }

    function test_crop() {
        $fo = new ImageObject($this->util->imageMake());
        $this->assertSame([50, 50], $fo->crop(50, 50)->dimensions());
    }

    function test_info() {
        $fo = new ImageObject($file = $this->util->imageMake());
        $info = $fo->info();

        $this->assertSame([200, 200], [$info['width'], $info['height']]);
        $this->assertSame('image/png', $info['mime']);
        $this->assertSame(IMAGETYPE_PNG, $info['type']);
        $this->assertSame(filesize($file), $info['size']);
        $this->assertSame('.png', $info['extension']);
    }

    function test_contents() {
        $fo = new ImageObject($file = $this->util->imageMake());
        $contents = file_get_contents($file);

        $this->assertSame($contents, $fo->setContents($contents)->getContents());
    }

    function test_checks() {
        $fo = new ImageObject($this->util->imageMake());

        $this->assertTrue($fo->isPng());
        $this->assertFalse($fo->isGif());
        $this->assertFalse($fo->isJpeg());
        $this->assertFalse($fo->isWebp());
    }

    function test_size() {
        $fo = new ImageObject($file = $this->util->imageMake());
        $this->assertSame(filesize($file), $fo->size());
    }

    function test_toString() {
        $fo = new ImageObject($file = $this->util->imageMake());
        $this->assertSame(file_get_contents($file), $fo->toString());
    }

    function test_toBase64() {
        $fo = new ImageObject($file = $this->util->imageMake());
        $this->assertSame(base64_encode(file_get_contents($file)), $fo->toBase64());
    }

    function test_toDataUrl() {
        $fo = new ImageObject($file = $this->util->imageMake());
        $this->assertSame('data:image/png;base64,' . base64_encode(file_get_contents($file)), $fo->toDataUrl());
    }

    function test_fromFile() {
        $fo = ImageObject::fromFile($this->util->imageMake());
        $this->assertInstanceOf(ImageObject::class, $fo);

        $this->expectException(ImageObjectException::class);
        $this->expectExceptionMessage('No file exists [file: absent-file]');
        ImageObject::fromFile('absent-file');
    }

    function test_fromString() {
        $fo = ImageObject::fromString($contents = file_get_contents($this->util->image()));
        $this->assertInstanceOf(ImageObject::class, $fo);
        $this->assertSame($contents, $fo->toString());
    }
}
