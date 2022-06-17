<?php declare(strict_types=1);
namespace test\froq\file\system;
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

        $fs = new FileSource(['overwrite' => true]);
        $fs->prepare(['file' => $file, 'directory' => dirname($file), 'name' => $name, 'extension' => 'txt']);
        $fs->save();

        // Inherits.
        $this->assertSame($file, $fs->getSource());
        $this->assertSame($info, $fs->getSourceInfo());
        $this->assertSame($path, $fs->getTarget());
        $this->assertSame($info['size'], $fs->getSize());
        $this->assertSame($info['mime'], $fs->getMime());
        $this->assertSame($info['name'], $fs->getName());
        $this->assertSame($info['extension'], $fs->getExtension());
    }

    function test_save() {
        $fs = (new FileSource)->prepare([
            'file' => $file = $this->util->fileMake(),
            'directory' => dirname($file)
        ]);

        $this->assertSame($fs->save(), $fs->getTarget());

        $this->expectException(FileSourceException::class);
        $this->expectExceptionMessage("Cannot overwrite on existing file `{$fs->getTarget()}`");
        $fs->save();
    }

    function test_move() {
        $fs = (new FileSource)->prepare([
            'file' => $file = $this->util->fileMake(),
            'directory' => dirname($file)
        ]);

        $this->assertSame($fs->move(), $fs->getTarget());

        $this->expectException(FileSourceException::class);
        $this->expectExceptionMessage("Cannot overwrite on existing file `{$fs->getTarget()}`");
        $fs->move();
    }

    function test_toString() {
        $fs = (new FileSource)->prepare([
            'file' => $file = $this->util->fileMake(),
            'directory' => dirname($file)
        ]);

        $this->assertStringEqualsFile($fs->save(), $fs->toString());
    }
}
