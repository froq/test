<?php declare(strict_types=1);
namespace test\froq\acl;
use froq\acl\Acl;

class AclTest extends \TestCase
{
    function test_nullUser() {
        $this->assertNull((new Acl)->getUser());
    }

    function test_nullRules() {
        $this->assertNull((new Acl)->getRules());
    }
}
