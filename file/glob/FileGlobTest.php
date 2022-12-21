<?php declare(strict_types=1);
namespace test\froq\file;
use froq\file\glob\{FileGlob, Glob};

class FileGlobTest extends \TestCase
{
    function init() {
        $this->util = $this->util('file');
    }

    function testConstructor() {
        $base = $this->util->dirMake();
        $this->util->dirMakeIn($base, 'dir', 3);
        $this->util->fileMakeIn($base, 'file', 5);

        $glob = new FileGlob($base . '/*');

        $this->assertCount(5, $glob);
        $this->assertSubclassOf(Glob::class, $glob);

        $glob->each(function ($info) {
            $this->assertTrue($info->isFile());
            $this->assertFalse($info->isDir());
        });
    }
}
