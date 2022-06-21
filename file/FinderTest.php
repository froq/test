<?php declare(strict_types=1);
namespace test\froq\file;
use froq\file\{Finder, FinderException};

class FinderTest extends \TestCase
{
    function setUp(): void {
        $this->util = $this->util('File');
    }

    function test_settersGetters() {
        $dir = tmp();
        $finder = new Finder();

        $this->assertNull($finder->getRoot());
        $this->assertNull($finder->getFileClass());
        $this->assertNull($finder->getInfoClass());

        $finder->setRoot($dir)
               ->setFileClass('SplFileObject')
               ->setInfoClass('SplFileInfo');

        $this->assertSame($dir, $finder->getRoot());
        $this->assertSame('SplFileObject', $finder->getFileClass());
        $this->assertSame('SplFileInfo', $finder->getInfoClass());

        $finder = new Finder($dir, 'SplFileObject', 'SplFileInfo');

        $this->assertSame($dir, $finder->getRoot());
        $this->assertSame('SplFileObject', $finder->getFileClass());
        $this->assertSame('SplFileInfo', $finder->getInfoClass());
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

    function test_checkRoot() {
        $finder = new Finder(tmp());
        $this->assertTrue($finder->checkRoot());

        $finder = new Finder('absent-dir');
        $this->assertFalse($finder->checkRoot());
    }

    function test_prepareRoot() {
        $weirdDir = $dir = DIRECTORY_SEPARATOR . tmp() . str_repeat(DIRECTORY_SEPARATOR, 3);
        $normalDir = realpath($dir) . DIRECTORY_SEPARATOR;

        $finder = new Finder($weirdDir);
        $this->assertSame($normalDir, $finder->prepareRoot());

        try {
            $finder = new Finder();
            $finder->prepareRoot(check: true);
        } catch (FinderException $e) {
            $this->assertSame("Root is empty yet, call setRoot()", $e->getMessage());
        }

        try {
            $finder = new Finder('absent-dir');
            $finder->prepareRoot(check: true);
        } catch (FinderException $e) {
            $this->assertSame("Root directory not exists: 'absent-dir'", $e->getMessage());
        }
    }
}
