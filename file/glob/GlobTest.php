<?php declare(strict_types=1);
namespace test\froq\file;
use froq\file\glob\{Glob, GlobException};

class GlobTest extends \TestCase
{
    function init() {
        $this->util = $this->util('file');
    }

    function testConstructor() {
        $this->expectException(GlobException::class);
        $this->expectExceptionMessageMatches('~null bytes~');
        new Glob("null-\0-byte-filename");
    }

    function testGet() {
        [$base, $dirs, $files] = $this->generate(3, 5);
        [$first, $last] = [first($dirs), last($files)];

        $glob = new Glob($base . '/*');

        $this->assertSame($first, $glob->get(0)->getPathName());
        $this->assertSame($last, $glob->get(7)->getPathName());

        $this->assertSame($first, $glob->getFirst()->getPathName());
        $this->assertSame($last, $glob->getLast()->getPathName());
    }

    public function testEach()
    {
        [$base, $dirs] = $this->generate(3);

        $glob = new Glob($base . '/*');

        $i = 0;
        $glob->each(function ($info) use ($dirs, &$i) {
            $this->assertSame($info->getPathName(), $dirs[$i++]);
        });
    }

    function testFilter() {
        [$base, $dirs, $files] = $this->generate(3, 1);

        $glob = new Glob($base . '/*');
        $this->assertCount(4, $glob);

        $glob1 = (clone $glob)->filter(fn($info) => $info->isDir());
        $this->assertCount(3, $glob1);

        $glob2 = (clone $glob)->filter(fn($info) => $info->isFile());
        $this->assertCount(1, $glob2);
    }

    function testMap() {
        [$base, $dirs] = $this->generate(3);

        $glob = new Glob($base . '/*');
        $glob->map(fn($info) => (string) $info);

        foreach ($glob as $i => $info) {
            $this->assertSame($dirs[$i], $info);
        }
    }

    function testReduce() {
        [$base, $dirs] = $this->generate(3);

        $glob = new Glob($base . '/*');
        $paths = $glob->reduce([], fn($paths, $info) => $paths = [...$paths, (string) $info]);

        $this->assertSame($dirs, $paths);
    }

    function testReverse() {
        [$base, $dirs] = $this->generate(3);

        $glob = new Glob($base . '/*');
        $paths = $glob->map('strval')->reverse()->toArray();

        $this->assertSame(array_reverse($dirs), $paths);
    }

    function testSort() {
        [$base, $dirs] = $this->generate(3);

        $glob = new Glob($base . '/*');
        $paths = $glob->map('strval')->sort(fn() => 1)->toArray();

        $this->assertSame(array_sort($dirs, fn() => 1), $paths);
    }

    function testSortSpecial() {
        [$base] = $this->generate(3, 2);

        $glob = new Glob($base . '/*');

        $this->assertFalse($glob->get(0)->isFile());

        // Move files up.
        $glob->sort(fn($a, $b) => $a->isDir() - $b->isDir());

        $this->assertTrue($glob->get(0)->isFile());
    }

    function testToArray() {
        [$base, $dirs] = $this->generate(3);

        $glob = new Glob($base . '/*');
        $paths = $glob->map('strval')->toArray();

        $this->assertSame($dirs, $paths);
    }

    function testToXArray() {
        [$base, $dirs] = $this->generate(3);

        $glob = new Glob($base . '/*');
        $paths = $glob->map('strval')->toXArray();

        $this->assertEquals(new \XArray($dirs), $paths);
    }

    function testCount() {
        [$base] = $this->generate(3);

        $glob = new Glob($base . '/*');

        $this->assertCount(3, $glob);
        $this->assertSame(3, count($glob));
        $this->assertSame(3, $glob->count());
    }

    function testIteratorGetters() {
        [$base] = $this->generate();

        $glob = new Glob($base . '/*');

        $this->assertInstanceOf(\Iterator::class, $glob->getIterator());
        $this->assertInstanceOf(\ArrayIterator::class, $glob->getArrayIterator());
    }

    function testArrayAccess() {
        [$base, $dirs] = $this->generate(1);

        $glob = new Glob($base . '/*');

        $this->assertTrue(isset($glob[0]));
        $this->assertSame($dirs[0], (string) $glob[0]);

        try {
            $glob[0] = 'foo';
        } catch (\Throwable $e) {
            $this->assertInstanceOf(\ReadonlyError::class, $e);
            $this->assertSame('Cannot modify readonly class froq\file\glob\Glob', $e->getMessage());
        }

        try {
            unset($glob[0]);
        } catch (\Throwable $e) {
            $this->assertInstanceOf(\ReadonlyError::class, $e);
            $this->assertSame('Cannot modify readonly class froq\file\glob\Glob', $e->getMessage());
        }
    }

    private function generate($dirCount = 0, $fileCount = 0) {
        // Base temp dir.
        $base = $this->util->dirMake();
        $dirs = $files = [];

        $dirCount && $dirs = $this->util->dirMakeIn($base, 'dir', $dirCount);
        $fileCount && $files = $this->util->fileMakeIn($base, 'file', $fileCount);

        return [$base, (array) $dirs, (array) $files];
    }
}
