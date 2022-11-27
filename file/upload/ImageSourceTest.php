<?php declare(strict_types=1);
namespace test\froq\file\upload;
use froq\file\upload\{ImageSource, ImageSourceException};

class ImageSourceTest extends \TestCase
{
    function setUp(): void {
        $this->util = $this->util('file');
    }

    function test_getters() {
        $file = $this->util->imageMake();
        $name = uuid();
        $path = dirname($file) .'/'. $name .'.png'; // Target path.
        $info = ['mime' => 'image/png', 'size' => 20541, 'name' => $name, 'extension' => 'png'];

        $fu = new ImageSource(['overwrite' => true]);
        $fu->prepare(['file' => $file, 'directory' => dirname($file), 'name' => $name, 'extension' => 'png']);
        // $fu->resample();
        $fu->save();

        // Inherits.
        $this->assertSame($file, $fu->getSource());
        $this->assertSame($info, $fu->getSourceInfo());
        $this->assertSame($path, $fu->getTarget());
        $this->assertSame($info['size'], $fu->getSize());
        $this->assertSame($info['mime'], $fu->getMime());
        $this->assertSame($info['name'], $fu->getName());
        $this->assertSame($info['extension'], $fu->getExtension());

        // Natives.
        $this->assertInstanceOf(\GdImage::class, $fu->getSourceImage());
        $this->assertInstanceOf(\GdImage::class, $fu->getTargetImage());
        $this->assertSame([200, 200], $fu->getDimensions());
        $this->assertSame([200, 200], $fu->getNewDimensions());
        $this->assertSame([
            0 => 200, 1 => 200, 2 => 3,
            3 => 'width="200" height="200"',
            'bits' => 8, 'mime' => 'image/png',
            'type' => 3, 'width' => 200, 'height' => 200,
        ], $fu->getInfo());

        // States.
        $this->assertTrue($fu->resized());
        $this->assertTrue($fu->usesGd());
        $this->assertFalse($fu->usesImagick());
    }

    function test_save() {
        $fu = (new ImageSource)->prepare([
            'file' => $file = $this->util->imageMake(),
            'directory' => dirname($file)
        ]);

        $this->assertSame($fu->save(), $fu->getTarget());

        $this->expectException(ImageSourceException::class);
        $this->expectExceptionMessage("Cannot overwrite on existing file '{$fu->getTarget()}'");
        $fu->save();
    }

    function test_move() {
        $fu = (new ImageSource)->prepare([
            'file' => $file = $this->util->imageMake(),
            'directory' => dirname($file)
        ]);

        $this->assertSame($fu->move(), $fu->getTarget());

        $this->expectException(ImageSourceException::class);
        $this->expectExceptionMessage("Cannot overwrite on existing file '{$fu->getTarget()}'");
        $fu->move();
    }

    function test_resample() {
        $fu = (new ImageSource)->prepare($this->util->imageMake());
        $fu->resample();

        $this->assertSame([200, 200], $fu->getDimensions());
        $this->assertSame([200, 200], $fu->getNewDimensions());
    }

    function test_resize() {
        $fu = (new ImageSource)->prepare($this->util->imageMake());
        $fu->resize(50, 50);

        $this->assertSame([50, 50], $fu->getDimensions());
        $this->assertSame([50, 50], $fu->getNewDimensions());
    }

    function test_resizeThumbnail() {
        $fu = (new ImageSource)->prepare($this->util->imageMake());
        $fu->resizeThumbnail(50, null);

        $this->assertSame([50, 50], $fu->getDimensions());
        $this->assertSame([50, 50], $fu->getNewDimensions());
    }

    function test_crop() {
        $fu = (new ImageSource)->prepare($this->util->imageMake());
        $fu->crop(50, 50);

        $this->assertSame([50, 50], $fu->getDimensions());
        $this->assertSame([50, 50], $fu->getNewDimensions());
    }

    function test_cropThumbnail() {
        $fu = (new ImageSource)->prepare($this->util->imageMake());
        $fu->cropThumbnail(50, 50);

        $this->assertSame([50, 50], $fu->getDimensions());
        $this->assertSame([50, 50], $fu->getNewDimensions());
    }

    function test_chop() {
        $fu = (new ImageSource)->prepare($this->util->imageMake());
        $fu->chop(50, 50, 0, 0);

        $this->assertSame([50, 50], $fu->getDimensions());
        $this->assertSame([50, 50], $fu->getNewDimensions());
    }

    function test_rotate() {
        $fu = (new ImageSource)->prepare($this->util->imageMake());
        $fu->rotate(45);

        $this->assertSame([283, 283], $fu->getDimensions());
        $this->assertSame([283, 283], $fu->getNewDimensions());
    }

    function test_toString() {
        $fu = (new ImageSource)->prepare([
            'file' => $file = $this->util->imageMake(),
            'directory' => dirname($file)
        ]);

        $this->assertStringEqualsFile($fu->save(), $fu->toString());
    }

    function test_toBase64() {
        $fu = (new ImageSource)->prepare($this->util->imageMake());
        $fu->resample();

        $this->assertSame(base64_encode($fu->toString()), $fu->toBase64());
    }

    function test_toDataUrl() {
        $fu = (new ImageSource)->prepare($this->util->imageMake());
        $fu->resample();

        $this->assertSame('data:image/png;base64,' . base64_encode($fu->toString()), $fu->toDataUrl());
    }
}
