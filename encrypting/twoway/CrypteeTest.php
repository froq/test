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

        $cod = new Cryptee($key, ['convert' => 'hex']);
        // $cod->setOption('convert', 'hex'); // Or later.

        $this->assertSame('15f0c24f5c0b', $cod->encrypt('Hello!'));
    }

    function test_decrypt() {
        $key = $this->key();
        $cod = new Cryptee($key);

        $this->assertSame('Hello!', $cod->decrypt('FfDCT1wL'));

        $cod = new Cryptee($key, ['convert' => 'hex']);
        // $cod->setOption('convert', 'hex'); // Or later.

        $this->assertSame('Hello!', $cod->decrypt('15f0c24f5c0b'));
    }

    private function key() {
        return 'wCVgRBB33PdH3HXZ9yAGuySHIaL0vohE';
    }
}
