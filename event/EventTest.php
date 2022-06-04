<?php declare(strict_types=1);
namespace froq\test\event;
use froq\event\{Event, EventException};

class EventTest extends \TestCase
{
    function test_constructor() {
        $event = new Event('foo', $callback = function () {});

        $this->assertSame('foo', $event->name);
        $this->assertSame($callback, $event->callback);
        $this->assertSame(true, $event->once);

        $this->expectException(EventException::class);
        $this->expectExceptionMessage('Empty event name');

        new Event('', function () {});
    }

    function test_invoke() {
        $event = new Event('foo', function ($arg = null) {
            return $arg;
        });

        $this->assertNull($event());
        $this->assertSame(123, $event(123));
    }

    function test_fire() {
        $event = new Event('foo', function ($arg = null) {
            return $arg;
        });

        $this->assertNull($event->fire());
        $this->assertSame(123, $event->fire(123));
    }

    function test_stack() {
        $event = new Event('foo', function () {});

        $this->assertNull($event->getStack());
    }
}
