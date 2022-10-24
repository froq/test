<?php declare(strict_types=1);
namespace test\froq\file;
use froq\file\glob\{DirectoryGlob, Glob};

class DirectoryGlobTest extends \TestCase
{
    function setUp(): void {
        $this->util = $this->util('file');
    }

    function test_constructor() {
        $base = $this->util->dirMake();
        $this->util->dirMakeIn($base, 'dir', 3);
        $this->util->fileMakeIn($base, 'file', 5);

        $glob = new DirectoryGlob($base . '/*');

        $this->assertCount(3, $glob);
        $this->assertSubclassOf(Glob::class, $glob);

        $glob->each(function ($info) {
            $this->assertTrue($info->isDir());
            $this->assertFalse($info->isFile());
        });
    }
}
