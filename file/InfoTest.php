<?php declare(strict_types=1);
namespace froq\test\file;
use froq\file\{Info, InfoException};
use froq\file\system\{Path, File, Directory};

class InfoTest extends \TestCase
{
    function test_constructor()
    {
        try {
            new Info("null-\0-byte-filename");
        } catch (InfoException $e) {
            $this->assertSame('Invalid path, path contains NULL-bytes', $e->getMessage());
        }

        try {
            new Info("");
        } catch (InfoException $e) {
            $this->assertSame('Invalid path, empty path given', $e->getMessage());
        }
    }

    function test_toString() {
        $info = new Info(__file__);

        $this->assertSame(__file__, $info->__toString());
        $this->assertSame(__file__, (string) $info);
    }

    function test_getters() {
        $info = new Info(__file__);

        $this->assertSame('file', $info->getType());
        $this->assertSame('text/x-php', $info->getMime());
        $this->assertSame('php', $info->getExtension());
        $this->assertSame(dirname(__file__), $info->getDirname());
        $this->assertSame(basename(__file__, '.php'), $info->getFilename());
    }

    function test_checkers() {
        $file = $this->file('test-file');
        $info = new Info($file);

        $this->assertFalse($info->exists());

        touch($file);

        $this->assertTrue($info->exists());
        $this->assertTrue($info->isAvailable());
        $this->assertTrue($info->isAvailableFor('write'));

        unlink($file);
    }

    function test_initers() {
        $file = $this->file('');
        $info = new Info($file);

        $this->assertInstanceOf(Path::class, $info->toPath());
        $this->assertInstanceOf(File::class, $info->toFile());
        $this->assertInstanceOf(Directory::class, $info->toDir());
        $this->assertInstanceOf(Directory::class, $info->toDirectory());
    }

    function test_converters() {
        $file = $this->file('', true);
        $info = new Info($file);

        $dirname = dirname($file);
        $basename = basename($file);
        $filename = filename($file); // @sugar

        $array = $info->toArray();
        $object = $info->toObject();

        $this->assertSame('file', $array['type']);
        $this->assertSame($file, $array['path']);
        $this->assertSame($file, $array['realpath']);
        $this->assertSame($dirname, $array['dirname']);
        $this->assertSame($basename, $array['basename']);
        $this->assertSame($filename, $array['filename']);
        $this->assertNull($array['extension']);

        $this->assertSame('file', $object->type);
        $this->assertSame($file, $object->path);
        $this->assertSame($file, $object->realpath);
        $this->assertSame($dirname, $object->dirname);
        $this->assertSame($basename, $object->basename);
        $this->assertSame($filename, $object->filename);
        $this->assertNull($object->extension);

        $this->drop($file);
    }

    function test_normalizePath() {
        $path = __dir__ . '/../';
        $realpath = realpath($path);

        $this->assertNotSame($path, Info::normalizePath($path));
        $this->assertSame($realpath, Info::normalizePath($path));
    }

    private function file($prefix, $create = false) {
        $file = tmp() . '/' . $prefix . suid(); // @sugar
        if ($create) {
            touch($file);
        }
        return $file;
    }

    private function drop(...$files) {
        foreach ($files as $file) {
            @unlink($file);
        }
    }
}
