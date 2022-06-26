<?php declare(strict_types=1);
namespace test\froq\event;
use froq\event\{Event, EventException};

class EventTest extends \TestCase
{
    function test_constructor() {
        $event = new Event('foo', function () {});

        $this->assertLength(36, $event->id); // UUID.
        $this->assertSame('foo', $event->name);
    }
    function test_states() {
        $target = $this;
        $once = true;
        $data = 123;
        $callback = function ($e) use ($target, $once, $data) {
            return $e->target === $target
                && $e->once === $once
                && $e->data === $data;
        };
        $event = new Event('foo', $callback, once: true, data: 123);

        $this->assertTrue($event->once);
        $this->assertSame(123, $event->data);
        $this->assertSame(null, $event->fired);
        $this->assertSame(true, $event());
        $this->assertSame(true, $event->fired);

        $this->expectException(EventException::class);
        $this->expectExceptionMessage("No state found such 'absentState'");

        $event->absentState;
    }

    function test_invoke() {
        $event = new Event('foo', fn($e, $arg = null) => $arg);

        $this->assertNull($event());
        $this->assertSame(123, $event(123));
    }

    function test_fire() {
        $event = new Event('foo', fn($e, $arg = null) => $arg);

        $this->assertNull($event->fire());
        $this->assertSame(123, $event->fire(123));
    }

    function test_manager() {
        $event = new Event('foo', fn() => null);

        $this->assertNull($event->getManager());
    }

    function test_return() {
        $event = new Event('foo', fn() => null);

        $this->assertNull($event->getReturnValue());

        $event = new Event('foo', fn() => null);
        $event->setReturnValue(123);

        $this->assertSame(123, $event->getReturnValue());

        $event = new Event('foo', fn($e, $arg) => $arg);
        $event(123);

        $this->assertSame(123, $event->getReturnValue());
    }

    function test_propagation() {
        $event = new Event('foo', function ($e, $arg) {
            $e->stopPropagation();
            return $arg;
        });

        $this->assertFalse($event->isPropagationStopped());

        $ret = $event(123); // Set once.
        $ret = $event(456);
        $ret = $event('abc');

        $this->assertTrue($event->isPropagationStopped());
        $this->assertSame(123, $ret);
    }
}
