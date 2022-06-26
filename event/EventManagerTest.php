<?php declare(strict_types=1);
namespace test\froq\event;
use froq\event\{EventManager, EventManagerException, Event};

class EventManagerTest extends \TestCase
{
    function test_events() {
        $eventManager = new EventManager();

        $this->assertFalse($eventManager->has('foo'));
        $this->assertCount(0, $eventManager->events());

        $eventManager->add('foo', function () {});
        $eventManager->add('bar', function () { return 1; });
        $eventManager->add('baz', new Event('baz', function () {}));

        $this->assertTrue($eventManager->has('foo'));
        $this->assertCount(3, $eventManager->events());

        $eventManager->remove('foo');

        $this->assertFalse($eventManager->has('foo'));
        $this->assertCount(2, $eventManager->events());
    }

    function test_fire() {
        $eventManager = new EventManager();

        $eventManager->add('foo', function () {});
        $eventManager->add('bar', function () { return 1; });

        $this->assertNull($eventManager->fire('foo'));
        $this->assertSame(1, $eventManager->fire('bar'));

        $this->expectException(EventManagerException::class);
        $this->expectExceptionMessage("No event found such 'baz'");

        $eventManager->fire('baz');
    }

    function test_createEvent() {
        $event = EventManager::createEvent('foo', function () {});

        $this->assertInstanceOf(Event::class, $event);
    }

    function test_fireEvent() {
        $event = new Event('foo', function () { return 123; });

        $this->assertSame(123, EventManager::fireEvent($event));
    }
}
