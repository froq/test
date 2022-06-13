<?php declare(strict_types=1);
namespace test\froq\event;
use froq\event\{EventStack, EventStackException, Event};

class EventStackTest extends \TestCase
{
    function test_stack() {
        $events = new EventStack();

        $this->assertFalse($events->has('foo'));
        $this->assertCount(0, $events->stack());

        $events->add('foo', function () {});
        $events->add('bar', function () { return 1; });
        $events->add('baz', new Event('baz', function () {}));

        $this->assertTrue($events->has('foo'));
        $this->assertCount(3, $events->stack());

        $events->remove('foo');

        $this->assertFalse($events->has('foo'));
        $this->assertCount(2, $events->stack());
    }

    function test_fire() {
        $events = new EventStack();

        $events->add('foo', function () {});
        $events->add('bar', function () { return 1; });

        $this->assertNull($events->fire('foo'));
        $this->assertSame(1, $events->fire('bar'));

        $this->expectException(EventStackException::class);
        $this->expectExceptionMessage('No event found in stack with name `baz`');

        $events->fire('baz');
    }
}
