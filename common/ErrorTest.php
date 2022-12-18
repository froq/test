<?php declare(strict_types=1);
namespace test\froq\common;
use froq\common\Error;
use froq\common\interface\Thrownable;
use froq\common\trait\ThrownableTrait;

class ErrorTest extends \TestCase
{
    function testConstructor() {
        $e = new Error();

        $this->assertInstanceOf(Thrownable::class, $e);
        $this->assertInstanceOf(\Throwable::class, $e);
        $this->assertArrayHasKey(ThrownableTrait::class, class_uses($e));
    }

    function testMessageParams() {
        $e = new Error("Error: param %s, type %t, quotes %q %Q", ['x', 1, 'a', 'b']);

        $this->assertSame('Error: param x, type int, quotes \'a\' "b"', $e->getMessage());
    }

    function testMagicGet() {
        $e = new Error('Error test', code: 1);

        $this->assertSame('Error test', $e->message);
        $this->assertSame(1, $e->code);
        $this->assertNull($e->cause);
    }

    function testCauseMethods() {
        $e = new Error();

        $this->assertNull($e->getCause());
        $this->assertCount(0, $e->getCauses());

        $e = new Error(cause: new \Error());

        $this->assertNotNull($e->getCause());
        $this->assertCount(1, $e->getCauses());
        $this->assertInstanceOf(\Error::class, $e->getCause());
    }
}
