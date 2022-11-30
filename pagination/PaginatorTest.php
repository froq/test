<?php declare(strict_types=1);
namespace test\froq\pagination;
use froq\pagination\Paginator;

class PaginatorTest extends \TestCase
{
    function test_constructor() {
        $paginator = new Paginator();

        $this->assertSame(1, $paginator->getPage());
        $this->assertSame(Paginator::PER_PAGE, $paginator->getPerPage());
        $this->assertSame(Paginator::PER_PAGE_MAX, $paginator->getPerPageMax());

        $paginator = new Paginator(2, 5, 10);

        $this->assertSame(2, $paginator->getPage());
        $this->assertSame(5, $paginator->getPerPage());
        $this->assertSame(10, $paginator->getPerPageMax());
    }

    function test_settersGetters() {
        $paginator = new Paginator();
        $paginator->setPage(2)
                  ->setPerPage(5)
                  ->setPerPageMax(10);

        $this->assertSame(2, $paginator->getPage());
        $this->assertSame(5, $paginator->getPerPage());
        $this->assertSame(10, $paginator->getPerPageMax());
    }

    function test_paginate() {
        $paginator = new Paginator();
        $totalRecords = 30;

        $paginator->setPage(1)->paginate($totalRecords);

        $this->assertSame(1, $paginator->getPage());
        $this->assertSame(null, $paginator->getPrevPage());
        $this->assertSame(2, $paginator->getNextPage());

        $paginator->setPage(2)->paginate($totalRecords);

        $this->assertSame(2, $paginator->getPage());
        $this->assertSame(1, $paginator->getPrevPage());
        $this->assertSame(3, $paginator->getNextPage());

        $this->assertSame(3, $paginator->getTotalPages());
        $this->assertSame(30, $paginator->getTotalRecords());
    }

    function test_toArray() {
        $paginator = new Paginator();

        $this->assertEquals([
            'page' => 1, 'perPage' => 10,
            'prevPage' => null, 'nextPage' => null,
            'totalPages' => null, 'totalRecords' => null
        ], $paginator->toArray());

        $paginator->paginate(30);

        $this->assertEquals([
            'page' => 1, 'perPage' => 10,
            'prevPage' => null, 'nextPage' => 2,
            'totalPages' => 3, 'totalRecords' => 30
        ], $paginator->toArray());
    }

    function test_toObject() {
        $paginator = new Paginator();

        $this->assertEquals((object) [
            'page' => 1, 'perPage' => 10,
            'prevPage' => null, 'nextPage' => null,
            'totalPages' => null, 'totalRecords' => null
        ], $paginator->toObject());

        $paginator->paginate(30);

        $this->assertEquals((object) [
            'page' => 1, 'perPage' => 10,
            'prevPage' => null, 'nextPage' => 2,
            'totalPages' => 3, 'totalRecords' => 30
        ], $paginator->toObject());
    }

    function test_jsonSerialize() {
        $paginator = new Paginator();

        $this->assertEquals(
            '{"page":1,"perPage":10,"prevPage":null,"nextPage":null,"totalPages":null,"totalRecords":null}',
            json_encode($paginator));

        $paginator->paginate(30);

        $this->assertEquals(
            '{"page":1,"perPage":10,"prevPage":null,"nextPage":2,"totalPages":3,"totalRecords":30}',
            json_encode($paginator));
    }
}
