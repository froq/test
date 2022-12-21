<?php declare(strict_types=1);
namespace test\froq\file\upload;
use froq\file\upload\{ImageSource, ImageSourceException, Source, SourceError};
use froq\file\FileException;

class ImageSourceTest extends \TestCase
{
    function init() {
        $this->util = $this->util('file');
    }

    function testConstructor() {
        $image = new ImageSource($src = $this->util->imageMake());

        $this->assertSame($src, $image->getSourceFile());

        try {
            new ImageSource(['file' => null]);
        } catch (ImageSourceException $e) {
            $this->assertSame('No source file given, "file" or "tmp_name" field cannot be empty',
                $e->getMessage());
        }

        try {
            new ImageSource(['file' => 'absent-file']);
        } catch (ImageSourceException $e) {
            $this->assertSame(FileException::class, $e->getCause()->getClass());
            $this->assertSame('Failed to open stream: No such file or directory',
                $e->getMessage());
        }

        try {
            new ImageSource(['error' => UPLOAD_ERR_INI_SIZE]);
        } catch (ImageSourceException $e) {
            $this->assertSame('The uploaded file exceeds the upload_max_filesize directive in php.ini',
                $e->getMessage());
        }
    }

    function testSave() {
        $image = new ImageSource($this->util->imageMake());
        $to = $this->util->dirMake(); // To directory.

        $this->assertSame($image->save($to), $image->getTargetFile());


        $name = $image->getName();
        $image = new ImageSource(['file' => $this->util->imageMake(), 'name' => $name],
            options: ['overwrite' => true]);

        $this->assertSame($image->save($to), $image->getTargetFile());

        $image = new ImageSource(['file' => $this->util->imageMake(), 'name' => $name]);

        $this->expectException(ImageSourceException::class);
        $this->expectExceptionMessageMatches('~Cannot overwrite on existing file~');
        $image->save($to);
    }

    function testMove() {
        $image = new ImageSource($this->util->imageMake());
        $to = $this->util->dirMake(); // To directory.

        $this->assertSame($image->move($to), $image->getTargetFile());

        $name = $image->getName();
        $image = new ImageSource(['file' => $this->util->imageMake(), 'name' => $name],
            options: ['overwrite' => true]);

        $this->assertSame($image->move($to), $image->getTargetFile());

        $image = new ImageSource(['file' => $this->util->imageMake(), 'name' => $name]);

        $this->expectException(ImageSourceException::class);
        $this->expectExceptionMessageMatches('~Cannot overwrite on existing file~');
        $image->move($to);
    }

    function testToString() {
        $file = new ImageSource($this->util->imageMake());
        $to = $this->util->dirMake(); // To directory.

        $this->assertStringEqualsFile($file->save($to), $file->toString());
    }

    function testToBase64() {
        $image = new ImageSource($this->util->imageMake());
        $image->resample();

        $this->assertSame(base64_encode($image->toString()), $image->toBase64());
    }

    function testToDataUrl() {
        $image = new ImageSource($this->util->imageMake());
        $image->resample();

        $this->assertSame('data:image/png;base64,' . base64_encode($image->toString()), $image->toDataUrl());
    }

    /** Manipulation Methods. */

    function testResample() {
        $image = new ImageSource($this->util->imageMake());
        $image->resample();

        $this->assertSame([200, 200], $image->getDimensions());
        $this->assertSame([200, 200], $image->getNewDimensions());
    }

    function testResize() {
        $image = new ImageSource($this->util->imageMake());
        $image->resize(50, 50);

        $this->assertSame([50, 50], $image->getDimensions());
        $this->assertSame([50, 50], $image->getNewDimensions());
    }

    function testResizeThumbnail() {
        $image = new ImageSource($this->util->imageMake());
        $image->resizeThumbnail(50, null);

        $this->assertSame([50, 50], $image->getDimensions());
        $this->assertSame([50, 50], $image->getNewDimensions());
    }

    function testCrop() {
        $image = new ImageSource($this->util->imageMake());
        $image->crop(50, 50);

        $this->assertSame([50, 50], $image->getDimensions());
        $this->assertSame([50, 50], $image->getNewDimensions());
    }

    function testCropThumbnail() {
        $image = new ImageSource($this->util->imageMake());
        $image->cropThumbnail(50, 50);

        $this->assertSame([50, 50], $image->getDimensions());
        $this->assertSame([50, 50], $image->getNewDimensions());
    }

    function testChop() {
        $image = new ImageSource($this->util->imageMake());
        $image->chop(50, 50, 0, 0);

        $this->assertSame([50, 50], $image->getDimensions());
        $this->assertSame([50, 50], $image->getNewDimensions());
    }

    function testRotate() {
        $image = new ImageSource($this->util->imageMake());
        $image->rotate(45);

        $this->assertSame([283, 283], $image->getDimensions());
        $this->assertSame([283, 283], $image->getNewDimensions());
    }

    /** Inherit Methods. */

    function testOptionChecks() {
        try {
            new ImageSource(__FILE__, ['maxFileSize' => 10 /* or 10b */]);
        } catch (ImageSourceException $e) {
            $this->assertSame('File size exceeded, "maxFileSize" option: 10 (10 bytes)',
                $e->getMessage());
            $this->assertSame(SourceError::OPTION_SIZE_EXCEEDED, $e->getCode());
        }

        try {
            new ImageSource(__FILE__, ['allowedMimes' => 'image/png']);
        } catch (ImageSourceException $e) {
            $this->assertSame('Mime text/x-php not allowed by "allowedMimes" option, allowed mimes: image/png',
                $e->getMessage());
            $this->assertSame(SourceError::OPTION_NOT_ALLOWED_MIME, $e->getCode());
        }

        try {
            new ImageSource(__FILE__, ['allowedExtensions' => 'png']);
        } catch (ImageSourceException $e) {
            $this->assertSame('Extension php not allowed by "allowedExtensions" option, allowed extensions: png',
                $e->getMessage());
            $this->assertSame(SourceError::OPTION_NOT_ALLOWED_EXTENSION, $e->getCode());
        }
    }

    function testGetterMethods() {
        $source = $this->util->imageMake();
        $image = new ImageSource(['file' => $source, 'name' => 'png-file']);

        $this->assertSame($source, $image->getSourceFile());
        $this->assertNull($image->getTargetFile());
        $this->assertSame('png-file', $image->getName());
        $this->assertSame(filesize($source), $image->getSize());
        $this->assertSame('image/png', $image->getMime());
        $this->assertSame('png', $image->getExtension());

        $source = $from = $this->util->imageMake();
        $image = new ImageSource(['file' => $source, 'name' => null]);
        $src = $from; $to = $this->util->dirMake(); // To directory.
        $dst = $image->save($to);

        $this->assertSame($src, $image->getSourceFile());
        $this->assertSame($dst, $image->getTargetFile());

        // Auto created as UUID.
        $this->assertTrue(\Uuid::validate($image->getName()));

        // Natives.
        $this->assertInstanceOf(\GdImage::class, $image->getSourceImage());
        $this->assertInstanceOf(\GdImage::class, $image->getTargetImage());
        $this->assertSame([200, 200], $image->getDimensions());
        $this->assertSame([200, 200], $image->getNewDimensions());
        $this->assertSame([
            0 => 200, 1 => 200, 2 => 3,
            3 => 'width="200" height="200"',
            'bits' => 8, 'mime' => 'image/png',
            'type' => 3, 'width' => 200, 'height' => 200,
        ], $image->getInfo());

        // States.
        $this->assertTrue($image->resized());
        $this->assertTrue($image->usesGd());
        $this->assertFalse($image->usesImagick());
    }

    function testCheckerMethods() {
        $source = $this->util->imageMake();
        $file = new ImageSource(
            file: ['file' => $source, 'mime' => 'image/png'],
            options: ['allowedMimes' => 'image/png', 'allowedExtensions' => 'png', 'maxFileSize' => '2MB']
        );

        $this->assertTrue($file->isAllowedSize(1024**2));
        $this->assertTrue($file->isAllowedMime('image/png'));
        $this->assertTrue($file->isAllowedExtension('png'));

        $this->assertFalse($file->isAllowedSize(1024**4));
        $this->assertFalse($file->isAllowedMime('text/x-php'));
        $this->assertFalse($file->isAllowedExtension('php'));
    }
}
