<?php declare(strict_types=1);
namespace test\froq\encrypting;
use froq\encrypting\Base64;

class Base64Test extends \TestCase
{
    function test_encode() {
        $this->assertSame('SGVsbG8h', Base64::encode('Hello!'));
    }

    function test_decode() {
        $this->assertSame('Hello!', Base64::decode('SGVsbG8h'));
    }

    function test_encodeUrlSafe() {
        $this->assertSame('SGVsbG8sIHdvcmxkIQ', Base64::encodeUrlSafe('Hello, world!'));
    }

    function test_decodeUrlSafe() {
        $this->assertSame('Hello, world!', Base64::decodeUrlSafe('SGVsbG8sIHdvcmxkIQ'));
    }
}
