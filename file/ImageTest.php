<?php declare(strict_types=1);
namespace test\froq\file;
use froq\file\{Image, ImageException};
use froq\file\upload\ImageSource;

class ImageTest extends \TestCase
{
    function setUp(): void {
        $this->util = $this->util('file');
    }

    function test_settersGetters() {
        $file = $this->util->imageMake();
        $directory = dirname($file);
        $image = new Image();
        $image->setFile($file)
              ->setName('test')
              ->setExtension('png')
              ->setDirectory($directory);

        $this->assertSame($file, $image->getFile());
        $this->assertSame('test', $image->getName());
        $this->assertSame('png', $image->getExtension());
        $this->assertSame($directory, $image->getDirectory());
    }

    function test_source() {
        $image = new Image($this->util->imageMake());

        $this->assertInstanceOf(ImageSource::class, $image->source());
        $this->assertInstanceOf(ImageSource::class, $image->source);

        $this->expectException(ImageException::class);
        $this->expectExceptionMessage('No source file given yet');
        (new Image)->source();
    }

    function test_save() {
        $image = new Image($file = $this->util->imageMake());
        $image->setName('test-save.png')
              ->setDirectory(dirname($file));

        $this->assertSame($image->save(), $image->source->getTarget());

        $this->expectException(ImageException::class);
        $this->expectExceptionMessage("Cannot overwrite on existing file `{$image->source->getTarget()}`");
        $image->save();
    }

    function test_move() {
        $image = new Image($file = $this->util->imageMake());
        $image->setName('test-move.png')
              ->setDirectory(dirname($file));

        $this->assertSame($image->move(), $image->source->getTarget());

        $this->expectException(ImageException::class);
        $this->expectExceptionMessage("Cannot overwrite on existing file `{$image->source->getTarget()}`");
        $image->move();
    }

    function test_resample() {
        $image = new Image($this->util->imageMake());
        $image->resample();

        $this->assertSame([200, 200], $image->source->getDimensions());
        $this->assertSame([200, 200], $image->source->getNewDimensions());
    }

    function test_resize() {
        $image = new Image($this->util->imageMake());
        $image->resize(50, 50);

        $this->assertSame([50, 50], $image->source->getDimensions());
        $this->assertSame([50, 50], $image->source->getNewDimensions());
    }

    function test_resizeThumbnail() {
        $image = new Image($this->util->imageMake());
        $image->resizeThumbnail(50, null);

        $this->assertSame([50, 50], $image->source->getDimensions());
        $this->assertSame([50, 50], $image->source->getNewDimensions());
    }

    function test_crop() {
        $image = new Image($this->util->imageMake());
        $image->crop(50, 50);

        $this->assertSame([50, 50], $image->source->getDimensions());
        $this->assertSame([50, 50], $image->source->getNewDimensions());
    }

    function test_cropThumbnail() {
        $image = new Image($this->util->imageMake());
        $image->cropThumbnail(50, 50);

        $this->assertSame([50, 50], $image->source->getDimensions());
        $this->assertSame([50, 50], $image->source->getNewDimensions());
    }

    function test_chop() {
        $image = new Image($this->util->imageMake());
        $image->chop(50, 50, 0, 0);

        $this->assertSame([50, 50], $image->source->getDimensions());
        $this->assertSame([50, 50], $image->source->getNewDimensions());
    }

    function test_rotate() {
        $image = new Image($this->util->imageMake());
        $image->rotate(45);

        $this->assertSame([283, 283], $image->source->getDimensions());
        $this->assertSame([283, 283], $image->source->getNewDimensions());
    }

    function test_size() {
        $image = new Image($this->util->imageMake());

        $this->assertSame(20541, $image->size());
    }

    function test_mime() {
        $image = new Image($this->util->imageMake());

        $this->assertSame('image/png', $image->mime());
    }

    function test_info() {
        $image = new Image($this->util->imageMake());

        $this->assertSame([
            0 => 200, 1 => 200, 2 => 3,
            3 => 'width="200" height="200"',
            'bits' => 8, 'mime' => 'image/png',
            'type' => 3, 'width' => 200, 'height' => 200,
        ], $image->info());
    }

    function test_toString() {
        $image = new Image($file = $this->util->imageMake());
        $image->setDirectory(dirname($file));

        $this->assertStringEqualsFile($image->save(), $image->toString());
    }

    function test_toBase64() {
        $image = new Image($this->util->imageMake());
        $image->resample();

        $this->assertSame(base64_encode($image->toString()), $image->toBase64());
    }

    function test_toDataUrl() {
        $image = new Image($this->util->imageMake());
        $image->resample();

        $this->assertSame('data:image/png;base64,' . base64_encode($image->toString()), $image->toDataUrl());
    }
}
