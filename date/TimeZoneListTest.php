<?php declare(strict_types=1);
namespace test\froq\date;
use froq\date\{TimeZoneList, TimeZoneException, TimeZoneInfo};

class TimeZoneListTest extends \TestCase
{
    function test_construction() {
        $list = new TimeZoneList('UTC');
        $this->assertCount(1, $list);
        $this->assertSame(1, $list->count());
        $this->assertInstanceOf(TimeZoneInfo::class, $list[0]);
        $this->assertInstanceOf(TimeZoneInfo::class, $list->first());

        $list = new TimeZoneList('PER_COUNTRY', 'TR');
        $this->assertCount(1, $list);
        $this->assertSame(1, $list->count());
        $this->assertInstanceOf(TimeZoneInfo::class, $list[0]);
        $this->assertInstanceOf(TimeZoneInfo::class, $list->first());

        $this->expectException(TimeZoneException::class);
        $this->expectExceptionMessageMatches('~^Invalid group foo~');
        new TimeZoneList('foo');
    }

    function test_list() {
        $list = TimeZoneList::list('UTC');
        $this->assertCount(1, $list);
        $this->assertInstanceOf(TimeZoneInfo::class, $list[0]);

        $list = TimeZoneList::list('PER_COUNTRY', 'TR');
        $this->assertCount(1, $list);
        $this->assertInstanceOf(TimeZoneInfo::class, $list[0]);
    }

    function test_listGroup() {
        $list = TimeZoneList::listGroup('UTC');
        $this->assertCount(1, $list);
        $this->assertInstanceOf(TimeZoneInfo::class, $list[0]);
    }

    function test_listCountry() {
        $list = TimeZoneList::listCountry('TR');
        $this->assertCount(1, $list);
        $this->assertInstanceOf(TimeZoneInfo::class, $list[0]);
    }
}
