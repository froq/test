<?php declare(strict_types=1);
namespace test\froq\file;
use froq\file\{Finder, FinderException};

class FinderTest extends \TestCase
{
    function setUp(): void {
        $this->util = $this->util('file');
    }

    function test_root() {
        $dir = tmp();
        $finder = new Finder();

        $this->assertNull($finder->getRoot());
        $this->assertSame($dir, $finder->setRoot($dir)->getRoot());

        $finder = new Finder($dir);
        $this->assertSame($dir, $finder->getRoot());
    }

    function test_find() {
        $finder = new Finder($dir = $this->util->dirMake());
        $pattern = '~.*~';

        $iter = $finder->find($pattern);
        $iter->next(); // For valid check.

        $this->assertFalse($iter->valid());
        $this->assertCount(0, $iter);
        $this->assertSame(0, iterator_count($iter));

        $files = $this->util->fileMakeIn($dir, '', 3);
        $count = count($files);

        $iter = $finder->find($pattern);
        $iter->next(); // For valid check.

        $this->assertTrue($iter->valid());
        $this->assertCount(3, $iter);
        $this->assertSame($count, iterator_count($iter));
    }

    function test_findAll() {
        $finder = new Finder($dir = $this->util->dirMake());
        $pattern = '~.*~';

        $iter = $finder->findAll($pattern);
        $iter->next(); // For valid check.

        $this->assertFalse($iter->valid());
        $this->assertCount(0, $iter);
        $this->assertSame(0, iterator_count($iter));

        $files = $this->util->fileMakeIn($dir, '', 3);
        $count = count($files);

        $iter = $finder->find($pattern);
        $iter->next(); // For valid check.

        $this->assertTrue($iter->valid());
        $this->assertCount(3, $iter);
        $this->assertSame($count, iterator_count($iter));
    }

    function test_glob() {
        $finder = new Finder($dir = $this->util->dirMake());
        $pattern = '*';

        $iter = $finder->glob($pattern);
        $iter->next(); // For valid check.

        $this->assertFalse($iter->valid());
        $this->assertCount(0, $iter);
        $this->assertSame(0, count($iter));

        $files = $this->util->fileMakeIn($dir, '', 3);
        $count = count($files);

        $iter = $finder->glob($pattern);
        $iter->next(); // For valid check.

        $this->assertTrue($iter->valid());
        $this->assertCount(3, $iter);
        $this->assertSame($count, count($iter));
    }

    function test_xglob() {
        $finder = new Finder($dir = $this->util->dirMake());
        $pattern = '*';

        // @var XArray
        $iter = $finder->xglob($pattern);

        $this->assertTrue($iter->isEmpty());
        $this->assertCount(0, $iter);
        $this->assertSame(0, count($iter));

        $files = $this->util->fileMakeIn($dir, '', 3);
        $count = count($files);

        $iter = $finder->xglob($pattern);

        $this->assertFalse($iter->isEmpty());
        $this->assertCount(3, $iter);
        $this->assertSame($count, count($iter));
    }
}
