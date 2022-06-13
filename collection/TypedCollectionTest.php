<?php declare(strict_types=1);
namespace test\froq\collection;
use froq\collection\{TypedCollection, CollectionException};

class TypedCollectionTest extends \TestCase
{
    function test_constructor() {
        try {
            new TypedCollection();
        } catch (CollectionException $e) {
            $this->assertStringContains('Data type is required', $e->getMessage());
        }

        try {
            new TypedCollection(['1'], dataType: 'int');
        } catch (CollectionException $e) {
            $this->assertStringContains('must be type of int', $e->getMessage());
        }
    }

    function test_alterMethodsWithTypeChecks() {
        $col = new TypedCollection([], 'int');

        $col->set('x', 1);
        $this->assertTrue($col->get('x') === 1);
        $this->assertTrue($col->get('y') === null);

        try {
            $col->set('y', '1');
        } catch (CollectionException $e) {
            $this->assertStringContains('must be type of int, string given', $e->getMessage());
        }
    }
}
