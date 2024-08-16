<?php declare(strict_types=1);
namespace test\froq\util;
use froq\util\Arrays;

class ArraysTest extends \TestCase
{
    function test_isListArray() {
        self::assertTrue(Arrays::isListArray([1, 2]));
        self::assertFalse(Arrays::isListArray(['a' => 1]));
    }

    function test_isAssocArray() {
        self::assertTrue(Arrays::isAssocArray(['a' => 1]));
        self::assertFalse(Arrays::isAssocArray([1, 2]));
    }

    function test_isMapArray() {
        self::assertTrue(Arrays::isMapArray(['a' => 1]));
        self::assertFalse(Arrays::isMapArray([1, 2]));
    }

    function test_isSetArray() {
        self::assertTrue(Arrays::isSetArray([1, 2]));
        self::assertTrue(Arrays::isSetArray(['a' => 1]));
        self::assertFalse(Arrays::isSetArray([1, 2, 2]));
    }

    // To be continued..
}
