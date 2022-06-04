<?php declare(strict_types=1);
namespace froq\test\encrypting\twoway;
use froq\encrypting\twoway\{OpenSsl, TwowayException};

class OpenSslTest extends \TestCase
{
    function setUp(): void {
        if (!extension_loaded('openssl')) {
            $this->markTestSkipped('OpenSSL extension not loaded, skipped test.');
        }
    }

    function test_constructor() {
        try {
            new OpenSsl('', '');
        } catch (TwowayException $e) {
            $this->assertStringStartsWith('Invalid key length', $e->getMessage());
        }

        try {
            new OpenSsl(str_repeat('a', 32), 'foo');
        } catch (TwowayException $e) {
            $this->assertStringStartsWith('Invalid cipher method', $e->getMessage());
        }
    }

    function test_encrypt() {
        $key = $this->key();
        $cod = new OpenSsl($key);

        // Cos it gives different hash for each call.
        $this->assertLength(strlen('KH06iLz/gWQg8BuDuk/Gg1eOX+AkWL2hTvgG6IAGCvlkiT58LjKbTM0keZYMIM45XgqDUmeb'),
            $cod->encrypt('Hello!'));
    }

    function test_decrypt() {
        $key = $this->key();
        $cod = new OpenSsl($key);

        $this->assertSame('Hello!',
            $cod->decrypt('KH06iLz/gWQg8BuDuk/Gg1eOX+AkWL2hTvgG6IAGCvlkiT58LjKbTM0keZYMIM45XgqDUmeb'));
    }

    private function key() {
        return 'wCVgRBB33PdH3HXZ9yAGuySHIaL0vohE';
    }
}
