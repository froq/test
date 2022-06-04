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

        // Cos it gives different hash for each call, test length simply.
        $this->assertLength(
            strlen('KH06iLz/gWQg8BuDuk/Gg1eOX+AkWL2hTvgG6IAGCvlkiT58LjKbTM0keZYMIM45XgqDUmeb'),
            $cod->encrypt('Hello!')
        );

        $cod = new OpenSsl($key, options: ['convert' => 'hex']);
        // $cod->setOption('convert', 'hex'); // Or later.

        $this->assertMatches('~^[a-f0-9]+$~', $cod->encrypt('Hello!'));
    }

    function test_decrypt() {
        $key = $this->key();
        $cod = new OpenSsl($key);

        $this->assertSame('Hello!',
            $cod->decrypt('KH06iLz/gWQg8BuDuk/Gg1eOX+AkWL2hTvgG6IAGCvlkiT58LjKbTM0keZYMIM45XgqDUmeb')
        );

        $cod = new OpenSsl($key, options: ['convert' => 'hex']);
        // $cod->setOption('convert', 'hex'); // Or later.

        $this->assertSame('Hello!', $cod->decrypt(
            '6f3ab2e4441467ac25ac4708be31c22eda0b7e870ca48e35fcfd18' .
            '0f9bd86d00f342f8624010d88e114391cff20f99835fbc59eb4d80'
        ));
    }

    private function key() {
        return 'wCVgRBB33PdH3HXZ9yAGuySHIaL0vohE';
    }
}
