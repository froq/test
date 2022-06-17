<?php declare(strict_types=1);
namespace test\froq\file\upload;
use froq\file\upload\{FileSource, FileSourceException};

class FileSourceTest extends \TestCase
{
    function setUp(): void {
        $this->util = $this->util('File');
    }

    function test_getters() {
        $file = $this->util->fileMake('', 'Hello!');
        $name = uuid();
        $path = dirname($file) .'/'. $name .'.txt'; // Target path.
        $info = ['mime' => 'text/plain', 'size' => 6, 'name' => $name, 'extension' => 'txt'];

        $fu = new FileSource(['overwrite' => true]);
        $fu->prepare(['file' => $file, 'directory' => dirname($file), 'name' => $name, 'extension' => 'txt']);
        $fu->save();

        // Inherits.
        $this->assertSame($file, $fu->getSource());
        $this->assertSame($info, $fu->getSourceInfo());
        $this->assertSame($path, $fu->getTarget());
        $this->assertSame($info['size'], $fu->getSize());
        $this->assertSame($info['mime'], $fu->getMime());
        $this->assertSame($info['name'], $fu->getName());
        $this->assertSame($info['extension'], $fu->getExtension());
    }

    function test_save() {
        $fu = (new FileSource)->prepare([
            'file' => $file = $this->util->fileMake(),
            'directory' => dirname($file)
        ]);

        $this->assertSame($fu->save(), $fu->getTarget());

        $this->expectException(FileSourceException::class);
        $this->expectExceptionMessage("Cannot overwrite on existing file `{$fu->getTarget()}`");
        $fu->save();
    }

    function test_move() {
        $fu = (new FileSource)->prepare([
            'file' => $file = $this->util->fileMake(),
            'directory' => dirname($file)
        ]);

        $this->assertSame($fu->move(), $fu->getTarget());

        $this->expectException(FileSourceException::class);
        $this->expectExceptionMessage("Cannot overwrite on existing file `{$fu->getTarget()}`");
        $fu->move();
    }

    function test_toString() {
        $fu = (new FileSource)->prepare([
            'file' => $file = $this->util->fileMake(),
            'directory' => dirname($file)
        ]);

        $this->assertStringEqualsFile($fu->save(), $fu->toString());
    }
}
