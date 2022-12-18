<?php declare(strict_types=1);
namespace test\froq\acl;
use froq\acl\{Acl, User};

class UserTest extends \TestCase
{
    function testNullUser() {
        $this->assertNull((new Acl)->getUser());
        $this->assertNotNull((new Acl(new User))->getUser());
    }

    function testUserId() {
        $this->assertSame(1, (new User(1))->getId());
        $this->assertSame(null, (new User(null))->getId());
    }

    function testUserIsLoggedIn() {
        $this->assertTrue((new User(1, 'Doe John'))->isLoggedIn());
        $this->assertFalse((new User(null, 'Foe John'))->isLoggedIn());
    }

    function testUserHasAccessTo() {
        $user1 = new User(1, 'Doe');
        $user2 = new User(2, 'Foe');

        $user1->setPermissionsOf('/posts', ['read', 'write']);
        $user2->setPermissionsOf('/posts', []);

        $this->assertTrue($user1->hasAccessTo('/posts'));
        $this->assertFalse($user2->hasAccessTo('/posts'));
    }

    function testUserCanRead() {
        $user1 = new User(1, 'Doe');
        $user2 = new User(2, 'Foe');

        $user1->setPermissionsOf('/posts', ['read']);
        $user2->setPermissionsOf('/posts', []);

        $this->assertTrue($user1->canRead('/posts'));
        $this->assertFalse($user2->canRead('/posts'));
    }

    function testUserCanWrite() {
        $user1 = new User(1, 'Doe');
        $user2 = new User(2, 'Foe');

        $user1->setPermissionsOf('/posts', ['write']);
        $user2->setPermissionsOf('/posts', []);

        $this->assertTrue($user1->canWrite('/posts'));
        $this->assertFalse($user2->canWrite('/posts'));
    }

    function testUserCanReadAndWrite() {
        $user1 = new User(1, 'Doe');
        $user2 = new User(2, 'Foe');

        // $user1->setPermissionsOf('/posts', ['all']); // Or.
        $user1->setPermissionsOf('/posts', ['read', 'write']);
        $user2->setPermissionsOf('/posts', []);

        $this->assertTrue($user1->canRead('/posts') or $user1->canWrite('/posts'));
        $this->assertFalse($user2->canRead('/posts') or $user2->canWrite('/posts'));
    }
}
