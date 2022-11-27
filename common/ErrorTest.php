<?php declare(strict_types=1);
namespace test\froq\common;
use froq\common\Error;
use froq\common\interface\Thrownable;
use froq\common\trait\ThrownableTrait;

class ErrorTest extends \TestCase
{
    function test_constructor() {
        $e = new Error();

        $this->assertInstanceOf(Thrownable::class, $e);
        $this->assertInstanceOf(\Throwable::class, $e);
        $this->assertArrayHasKey(ThrownableTrait::class, class_uses($e));
    }

    function test_messageParams() {
        $e = new Error("Error: param %s, type %t, quotes %q %Q", ['x', 1, 'a', 'b']);

        $this->assertSame('Error: param x, type int, quotes \'a\' "b"', $e->getMessage());
    }

    function test_magicGet() {
        $e = new Error('Error test', code: 1);

        $this->assertSame('Error test', $e->message);
        $this->assertSame(1, $e->code);
        $this->assertNull($e->cause);
    }

    function test_causeMethods() {
        $e = new Error();

        $this->assertNull($e->getCause());
        $this->assertCount(0, $e->getCauses());

        $e = new Error(cause: new \Error());

        $this->assertNotNull($e->getCause());
        $this->assertCount(1, $e->getCauses());
        $this->assertInstanceOf(\Error::class, $e->getCause());
    }
}
