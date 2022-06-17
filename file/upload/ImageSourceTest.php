<?php declare(strict_types=1);
namespace test\froq\file\system;
use froq\file\upload\{ImageSource, ImageSourceException};

class ImageSourceTest extends \TestCase
{
    function setUp(): void {
        $this->util = $this->util('File');
    }

    function test_getters() {
        $file = $this->util->imageMake();
        $path = dirname($file) .'/'. basename($file); // Target path.
        $name = basename($file, '.png');
        $info = ['mime' => 'image/png', 'size' => 20541, 'name' => $name, 'extension' => 'png'];

        $is = new ImageSource(['overwrite' => true]);
        $is->prepare(['file' => $file, 'directory' => dirname($file), 'name' => basename($file)]);
        // $is->resample();
        $is->save();

        // Inherits.
        $this->assertSame($file, $is->getSource());
        $this->assertSame($info, $is->getSourceInfo());
        $this->assertSame($path, $is->getTarget());
        $this->assertSame($info['size'], $is->getSize());
        $this->assertSame($info['mime'], $is->getMime());
        $this->assertSame($info['name'], $is->getName());
        $this->assertSame($info['extension'], $is->getExtension());

        // Natives.
        $this->assertInstanceOf(\GdImage::class, $is->getSourceImage());
        $this->assertInstanceOf(\GdImage::class, $is->getTargetImage());
        $this->assertSame([200, 200], $is->getDimensions());
        $this->assertSame([200, 200], $is->getNewDimensions());
        $this->assertSame([
            0 => 200, 1 => 200, 2 => 3,
            3 => 'width="200" height="200"',
            'bits' => 8, 'mime' => 'image/png',
            'type' => 3, 'width' => 200, 'height' => 200,
        ], $is->getInfo());

        // States.
        $this->assertTrue($is->resized());
        $this->assertTrue($is->usesGd());
        $this->assertFalse($is->usesImagick());
    }

    function test_save() {
        $is = (new ImageSource)->prepare([
            'file' => $file = $this->util->imageMake(),
            'name' => uuid(), 'directory' => dirname($file)
        ]);

        $this->assertSame($is->save(), $is->getTarget());

        $this->expectException(ImageSourceException::class);
        $this->expectExceptionMessage("Cannot overwrite on existing file `{$is->getTarget()}`");
        $is->save();
    }

    function test_move() {
        $is = (new ImageSource)->prepare([
            'file' => $file = $this->util->imageMake(),
            'name' => uuid(), 'directory' => dirname($file)
        ]);

        $this->assertSame($is->move(), $is->getTarget());

        $this->expectException(ImageSourceException::class);
        $this->expectExceptionMessage("Cannot overwrite on existing file `{$is->getTarget()}`");
        $is->move();
    }

    function test_resample() {
        $is = (new ImageSource)->prepare([
            'file' => $file = $this->util->imageMake(),
            'name' => uuid(), 'directory' => dirname($file)
        ]);

        $is->resample();

        $this->assertSame([200, 200], $is->getDimensions());
        $this->assertSame([200, 200], $is->getNewDimensions());
    }

    function test_resize() {
        $is = (new ImageSource)->prepare([
            'file' => $file = $this->util->imageMake(),
            'name' => uuid(), 'directory' => dirname($file)
        ]);

        $is->resize(50, 50);

        $this->assertSame([50, 50], $is->getDimensions());
        $this->assertSame([50, 50], $is->getNewDimensions());
    }

    function test_resizeThumbnail() {
        $is = (new ImageSource)->prepare([
            'file' => $file = $this->util->imageMake(),
            'name' => uuid(), 'directory' => dirname($file)
        ]);

        $is->resizeThumbnail(50, null);

        $this->assertSame([50, 50], $is->getDimensions());
        $this->assertSame([50, 50], $is->getNewDimensions());
    }

    function test_crop() {
        $is = (new ImageSource)->prepare([
            'file' => $file = $this->util->imageMake(),
            'name' => uuid(), 'directory' => dirname($file)
        ]);

        $is->crop(50, 50);

        $this->assertSame([50, 50], $is->getDimensions());
        $this->assertSame([50, 50], $is->getNewDimensions());
    }

    function test_cropThumbnail() {
        $is = (new ImageSource)->prepare([
            'file' => $file = $this->util->imageMake(),
            'name' => uuid(), 'directory' => dirname($file)
        ]);

        $is->cropThumbnail(50, 50);

        $this->assertSame([50, 50], $is->getDimensions());
        $this->assertSame([50, 50], $is->getNewDimensions());
    }

    function test_chop() {
        $is = (new ImageSource)->prepare([
            'file' => $file = $this->util->imageMake(),
            'name' => uuid(), 'directory' => dirname($file)
        ]);

        $is->chop(50, 50, 0, 0);

        $this->assertSame([50, 50], $is->getDimensions());
        $this->assertSame([50, 50], $is->getNewDimensions());
    }

    function test_rotate() {
        $is = (new ImageSource)->prepare([
            'file' => $file = $this->util->imageMake(),
            'name' => uuid(), 'directory' => dirname($file)
        ]);

        $is->rotate(45);

        $this->assertSame([283, 283], $is->getDimensions());
        $this->assertSame([283, 283], $is->getNewDimensions());
    }

    function test_toString() {
        $is = (new ImageSource)->prepare([
            'file' => $file = $this->util->imageMake(),
            'name' => uuid(), 'directory' => dirname($file)
        ]);

        $this->assertStringEqualsFile($is->save(), $is->toString());
    }

    function test_toBase64() {
        $is = (new ImageSource)->prepare([
            'file' => $file = $this->util->imageMake(),
            'name' => uuid(), 'directory' => dirname($file)
        ]);

        $this->assertSame(base64_encode(file_get_contents($is->save())), $is->toBase64());
    }

    function test_toDataUrl() {
        $is = (new ImageSource)->prepare([
            'file' => $file = $this->util->imageMake(),
            'name' => uuid(), 'directory' => dirname($file)
        ]);

        $this->assertSame('data:image/png;base64,' . base64_encode(file_get_contents($is->save())), $is->toDataUrl());
    }
}
