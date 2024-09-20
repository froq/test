<?php declare(strict_types=1);
namespace test\froq\file\upload;
use froq\file\upload\{FileSource, FileSourceException, Source, SourceError};
use froq\file\FileException;

class FileSourceTest extends \TestCase
{
    function init() {
        $this->util = $this->util('file');
    }

    function testConstructor() {
        $file = new FileSource($src = $this->util->fileMake());

        $this->assertSame($src, $file->getSourceFile());

        try {
            new FileSource(['file' => null]);
        } catch (FileSourceException $e) {
            $this->assertSame('No source file given, "file" or "tmp_name" field cannot be empty',
                $e->getMessage());
        }

        try {
            new FileSource(['file' => 'absent-file']);
        } catch (FileSourceException $e) {
            $this->assertSame(FileException::class, $e->getCause()->getClass());
            $this->assertSame('Failed to open stream: No such file or directory',
                $e->getMessage());
        }

        try {
            new FileSource(['error' => UPLOAD_ERR_INI_SIZE]);
        } catch (FileSourceException $e) {
            $this->assertSame('Uploaded file exceeds upload_max_filesize directive in php.ini',
                $e->getMessage());
        }
    }

    function testSave() {
        $file = new FileSource($this->util->fileMake());
        $to = $this->util->dirMake(); // To directory.

        $this->assertSame($file->save($to), $file->getTargetFile());


        $name = $file->getName();
        $file = new FileSource(['file' => $this->util->fileMake(), 'name' => $name],
            options: ['overwrite' => true]);

        $this->assertSame($file->save($to), $file->getTargetFile());

        $file = new FileSource(['file' => $this->util->fileMake(), 'name' => $name]);

        $this->expectException(FileSourceException::class);
        $this->expectExceptionMessageMatches('~Cannot overwrite on existing file~');
        $file->save($to);
    }

    function testMove() {
        $file = new FileSource($this->util->fileMake());
        $to = $this->util->dirMake(); // To directory.

        $this->assertSame($file->move($to), $file->getTargetFile());

        $name = $file->getName();
        $file = new FileSource(['file' => $this->util->fileMake(), 'name' => $name],
            options: ['overwrite' => true]);

        $this->assertSame($file->move($to), $file->getTargetFile());

        $file = new FileSource(['file' => $this->util->fileMake(), 'name' => $name]);

        $this->expectException(FileSourceException::class);
        $this->expectExceptionMessageMatches('~Cannot overwrite on existing file~');
        $file->move($to);
    }

    function testToString() {
        $file = new FileSource($this->util->fileMake());
        $to = $this->util->dirMake(); // To directory.

        $this->assertStringEqualsFile($file->save($to), $file->toString());
    }

    /** Inherit Methods. */

    function testOptionChecks() {
        try {
            new FileSource(__FILE__, ['maxFileSize' => 10 /* or 10b */]);
        } catch (FileSourceException $e) {
            $this->assertSame('File size exceeded, "maxFileSize" option: 10 (10 bytes)',
                $e->getMessage());
            $this->assertSame(SourceError::OPTION_SIZE_EXCEEDED, $e->getCode());
        }

        try {
            new FileSource(__FILE__, ['allowedMimes' => 'image/png']);
        } catch (FileSourceException $e) {
            $this->assertSame('Mime text/x-php not allowed by "allowedMimes" option, allowed mimes: image/png',
                $e->getMessage());
            $this->assertSame(SourceError::OPTION_NOT_ALLOWED_MIME, $e->getCode());
        }

        try {
            new FileSource(__FILE__, ['allowedExtensions' => 'png']);
        } catch (FileSourceException $e) {
            $this->assertSame('Extension php not allowed by "allowedExtensions" option, allowed extensions: png',
                $e->getMessage());
            $this->assertSame(SourceError::OPTION_NOT_ALLOWED_EXTENSION, $e->getCode());
        }
    }

    function testGetterMethods() {
        $source = $this->util->fileMake('', contents: 'Hello!');
        $file = new FileSource(['file' => $source, 'name' => 'txt-file']);

        $this->assertSame($source, $file->getSourceFile());
        $this->assertNull($file->getTargetFile());
        $this->assertSame('txt-file', $file->getName());
        $this->assertSame(filesize($source), $file->getSize());
        $this->assertSame('text/plain', $file->getMime());
        $this->assertSame('txt', $file->getExtension());

        $file = new FileSource(['file' => $from = $this->util->fileMake(), 'name' => null]);
        $src = $from; $to = $this->util->dirMake(); // To directory.
        $dst = $file->save($to);

        $this->assertSame($src, $file->getSourceFile());
        $this->assertSame($dst, $file->getTargetFile());
    }

    function testCheckerMethods() {
        $source = $this->util->fileMake('', contents: 'Hello!');
        $file = new FileSource(
            file: ['file' => $source, 'mime' => 'text/plain'],
            options: ['allowedMimes' => 'text/plain', 'allowedExtensions' => 'txt', 'maxFileSize' => '2MB']
        );

        $this->assertTrue($file->isAllowedSize(1024**2));
        $this->assertTrue($file->isAllowedMime('text/plain'));
        $this->assertTrue($file->isAllowedExtension('txt'));

        $this->assertFalse($file->isAllowedSize(1024**4));
        $this->assertFalse($file->isAllowedMime('text/x-php'));
        $this->assertFalse($file->isAllowedExtension('php'));
    }
}
