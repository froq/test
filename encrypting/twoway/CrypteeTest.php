<?php declare(strict_types=1);
namespace froq\test\encrypting\twoway;
use froq\encrypting\twoway\{Cryptee, TwowayException};

class CrypteeTest extends \TestCase
{
    function test_constructor() {
        try {
            new Cryptee('');
        } catch (TwowayException $e) {
            $this->assertStringStartsWith('Invalid key length', $e->getMessage());
        }
    }

    function test_encrypt() {
        $key = $this->key();
        $cod = new Cryptee($key);

        $this->assertSame('FfDCT1wL', $cod->encrypt('Hello!'));
    }

    function test_decrypt() {
        $key = $this->key();
        $cod = new Cryptee($key);

        $this->assertSame('Hello!', $cod->decrypt('FfDCT1wL'));
    }

    private function key() {
        return 'wCVgRBB33PdH3HXZ9yAGuySHIaL0vohE';
    }
}
