<?php declare(strict_types=1);
namespace test\froq\acl;
use froq\acl\Acl;

class AclTest extends \TestCase
{
    function testNullUser() {
        $this->assertNull((new Acl)->getUser());
    }

    function testNullRules() {
        $this->assertNull((new Acl)->getRules());
    }
}
