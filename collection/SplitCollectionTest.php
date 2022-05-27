<?php
namespace froq\test\collection;
use froq\collection\{SplitCollection, CollectionException};

class SplitCollectionTest extends \PHPUnit\Framework\TestCase
{
    function test_constructor() {
        $this->assertCount(5, new SplitCollection('a.b.c', ''));
        $this->assertCount(3, new SplitCollection('a.b.c', '.'));
    }

    function test_splitMethods() {
        $this->assertCount(5, SplitCollection::split('a.b.c', ''));
        $this->assertCount(3, SplitCollection::split('a.b.c', '.'));

        $this->assertCount(3, SplitCollection::splitRegExp('a.b.c', '~\.~'));
        $this->assertCount(3, SplitCollection::splitRegExp('a.b.c', '~\W~'));

        try {
            SplitCollection::splitRegExp('a.b.c', '~\.');
        } catch (\Throwable $e) {
            $this->assertInstanceOf(CollectionException::class, $e);
            $this->assertInstanceOf(\RegExpError::class, $e->getCause());
            $this->assertStringContainsString('No end delimiter ~', $e->getMessage());
        }
    }
}
