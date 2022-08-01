<?php declare(strict_types=1);
namespace test\froq\pager;
use froq\pager\Pager;

class PagerTest extends \TestCase
{
    function test_defaultAttributes() {
        $pager = new Pager();
        $this->assertSame(0, $pager->start);
        $this->assertSame(10, $pager->stop);
        $this->assertSame(1000, $pager->stopMax);
        $this->assertSame(10, $pager->stopDefault);
        $this->assertSame('s', $pager->startKey);
        $this->assertSame('ss', $pager->stopKey);
        $this->assertNull($pager->totalPages);
        $this->assertNull($pager->totalRecords);
        $this->assertFalse($pager->numerateFirstLast);
        $this->assertTrue($pager->autorun);
        $this->assertFalse($pager->redirect);
    }

    function test_customAttributes() {
        $count = 123; // Eg: count of books.
        $attributes = [
            'start'             => 0,
            'stop'              => 2, // Per page.
            'stopMax'           => 10, // Per page max.
            'stopDefault'       => 5,
            'startKey'          => 'page', // Eg: ?page=1.
            'stopKey'           => 'limit', // Eg: ?limit=5.
            'totalPages'        => null, // Will be calculated.
            'totalRecords'      => $count,
            'numerateFirstLast' => true,
            'autorun'           => false,
            'redirect'          => true,
        ];

        $pager = new Pager($attributes);
        $this->assertSame(0, $pager->start);
        $this->assertSame(2, $pager->stop);
        $this->assertSame(10, $pager->stopMax);
        $this->assertSame(5, $pager->stopDefault);
        $this->assertSame('page', $pager->startKey);
        $this->assertSame('limit', $pager->stopKey);
        $this->assertNull($pager->totalPages);
        $this->assertSame(123, $pager->totalRecords);
        $this->assertTrue($pager->numerateFirstLast);
        $this->assertFalse($pager->autorun);
        $this->assertTrue($pager->redirect);

        $pager->run(); // Because "autorun".
        $this->assertSame(62, $pager->totalPages);
    }

    function test_aliasStuff() {
        $pager = new Pager();
        $this->assertSame(10, $pager->getLimit());
        $this->assertSame(0, $pager->getOffset());

        $pager = new Pager(['limit' => 5, 'page' => 1]);
        $this->assertSame(5, $pager->getLimit());
        $this->assertSame(1, $pager->getOffset());
    }

    function test_currentStuff() {
        $pager = new Pager();
        $this->assertSame(1, $pager->getCurrent());

        $pager = new Pager(['limit' => 5, 'page' => 5]);
        $this->assertSame(2, $pager->getCurrent());
    }

    function test_run() {
        $pager = new Pager();
        $pager->run();
        $this->assertSame(1, $pager->totalPages);
        $this->assertSame(null, $pager->totalRecords);

        $pager = new Pager(['count' => 12, 'limit' => 3]);
        $pager->run();
        $this->assertSame(4, $pager->totalPages);
        $this->assertSame(12, $pager->totalRecords);
    }

    function test_toArray() {
        $pager = new Pager(['count' => 12, 'limit' => 3]);
        $pager->run();

        $this->assertSame([
            'limit' => 3,
            'offset' => 0,
            'current' => 1,
            'prev' => NULL,
            'next' => 2,
            'totalPages' => 4,
            'totalRecords' => 12,
        ], $pager->toArray());
    }

    function test_toObject() {
        $pager = new Pager(['count' => 12, 'limit' => 3]);
        $pager->run();

        $this->assertEquals((object) [
            'limit' => 3,
            'offset' => 0,
            'current' => 1,
            'prev' => NULL,
            'next' => 2,
            'totalPages' => 4,
            'totalRecords' => 12,
        ], $pager->toObject());
    }

    function test_jsonSerialize() {
        $pager = new Pager(['count' => 12, 'limit' => 3]);
        $pager->run();

        $this->assertSame(
            '{"limit":3,"offset":0,"current":1,"prev":null,"next":2,"totalPages":4,"totalRecords":12}',
            json_encode($pager)
        );
    }
}
