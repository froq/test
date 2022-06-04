<?php declare(strict_types=1);
namespace froq\test\encrypting;
use froq\encrypting\{Crypt, CryptException};

class CryptTest extends \TestCase
{
    function test_encrypt() {
        [$pp, $iv] = $this->secrets();

        $this->assertSame('Z1xhQCgs', Crypt::encrypt('Hello!', $pp, $iv));
        $this->assertSame('7K5Z19tyLT5', Crypt::encrypt('Hello!', $pp, $iv, encode: true));

        try {
            Crypt::encrypt('input', pp: '123', iv: '');
        } catch (CryptException $e) {
            $this->assertStringContains('Argument $iv length must be 16', $e->getMessage());
        }
    }

    function test_decrypt() {
        [$pp, $iv] = $this->secrets();

        $this->assertSame('Hello!', Crypt::decrypt('Z1xhQCgs', $pp, $iv));
        $this->assertSame('Hello!', Crypt::decrypt('7K5Z19tyLT5', $pp, $iv, decode: true));

        try {
            Crypt::decrypt('input', pp: '123', iv: '');
        } catch (CryptException $e) {
            $this->assertStringContains('Argument $iv length must be 16', $e->getMessage());
        }
    }

    private function secrets() {
        return ['password', 'TdBpoo3XjZcbe9zR'];
    }
}
