<?php declare(strict_types=1);
namespace test\froq\collection\iterator;
use froq\collection\iterator\{GeneratorIterator, GeneratorIteratorException};

class GeneratorIteratorTest extends \TestCase
{
    function test_constructor() {
        $it = new GeneratorIterator(['a', 'b']);
        $this->assertSame(['a', 'b'], $it->toArray());

        $it = new GeneratorIterator(new \XArray(['a', 'b']));
        $this->assertSame(['a', 'b'], $it->toArray());
    }

    function test_setGet() {
        $it = new GeneratorIterator();
        $it->setGenerator(fn() => yield 1);

        $this->assertInstanceOf(\Closure::class, $it->getGenerator());

        $this->expectException(GeneratorIteratorException::class);
        $it = new GeneratorIterator();
        $it->getGenerator();
    }

    function test_apply() {
        $it = new GeneratorIterator(['a', 'b']);
        $it->apply('upper');
        $this->assertSame(['a', 'b'], $it->toArray());

        $it = $it->apply('upper');
        $this->assertSame(['A', 'B'], $it->toArray());
    }

    function test_each() {
        $it = new GeneratorIterator($array = ['a', 'b']);
        $it->each(function ($value, $key) use ($array) {
            $this->assertSame($array[$key], $value);
        });
    }

    function test_toArray() {
        $it = new GeneratorIterator(['a', 'b']);
        $this->assertSame(['a', 'b'], $it->toArray());
    }

    function test_toList() {
        $it = new GeneratorIterator(['a' => 1, 'b' => 2]);
        $this->assertSame([1, 2], $it->toList());
    }

    function test_count() {
        $it = new GeneratorIterator([1]);
        $this->assertSame(1, $it->count());
        $this->assertCount(1, $it);
    }
}
