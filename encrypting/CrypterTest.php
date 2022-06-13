<?php declare(strict_types=1);
namespace test\froq\encrypting;
use froq\encrypting\{Crypter, CryptException};

class CrypterTest extends \TestCase
{
    function test_encrypt() {
        [$pp, $iv] = $this->secrets();
        $crypter = new Crypter($pp, $iv);

        $this->assertSame('Z1xhQCgs', $crypter->encrypt('Hello!'));
        $this->assertSame('7K5Z19tyLT5', $crypter->encrypt('Hello!', encode: true));

        try {
            $crypter = new Crypter(pp: '123', iv: '');
            $crypter->encrypt('input');
        } catch (CryptException $e) {
            $this->assertStringContains('Argument $iv length must be 16', $e->getMessage());
        }
    }

    function test_decrypt() {
        [$pp, $iv] = $this->secrets();
        $crypter = new Crypter($pp, $iv);

        $this->assertSame('Hello!', $crypter->decrypt('Z1xhQCgs'));
        $this->assertSame('Hello!', $crypter->decrypt('7K5Z19tyLT5', decode: true));

        try {
            $crypter = new Crypter(pp: '123', iv: '');
            $crypter->decrypt('input');
        } catch (CryptException $e) {
            $this->assertStringContains('Argument $iv length must be 16', $e->getMessage());
        }
    }

    private function secrets() {
        return ['password', 'TdBpoo3XjZcbe9zR'];
    }
}
