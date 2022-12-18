<?php declare(strict_types=1);
namespace test\froq\common;
use froq\common\Exception;
use froq\common\interface\Thrownable;
use froq\common\trait\ThrownableTrait;

class ExceptionTest extends \TestCase
{
    function testConstructor() {
        $e = new Exception();

        $this->assertInstanceOf(Thrownable::class, $e);
        $this->assertInstanceOf(\Throwable::class, $e);
        $this->assertArrayHasKey(ThrownableTrait::class, class_uses($e));
    }

    function testMessageParams() {
        $e = new Exception("Exception: param %s, type %t, quotes %q %Q", ['x', 1, 'a', 'b']);

        $this->assertSame('Exception: param x, type int, quotes \'a\' "b"', $e->getMessage());
    }

    function testMagicGet() {
        $e = new Exception('Exception test', code: 1);

        $this->assertSame('Exception test', $e->message);
        $this->assertSame(1, $e->code);
        $this->assertNull($e->cause);
    }

    function testCauseMethods() {
        $e = new Exception();

        $this->assertNull($e->getCause());
        $this->assertCount(0, $e->getCauses());

        $e = new Exception(cause: new \Exception());

        $this->assertNotNull($e->getCause());
        $this->assertCount(1, $e->getCauses());
        $this->assertInstanceOf(\Exception::class, $e->getCause());
    }
}
