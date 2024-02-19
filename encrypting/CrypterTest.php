<?php declare(strict_types=1);
namespace test\froq\encrypting;
use froq\encrypting\{Crypter, CryptException};

class CrypterTest extends \TestCase
{
    const PASSPHRASE = 'IzARZtq./mU}H|D&y~^Z5~Lr_y}i:Y:-q*Vr]n5-}0ydXcu31\0Nu?[Q';

    function testEncrypt() {
        $crypter = new Crypter(self::PASSPHRASE);

        $this->assertSame('cPpE8Rn0', $crypter->encrypt('Hello!'));
        $this->assertSame('8wE7W4vb5oQ', $crypter->encrypt('Hello!', encode: true));

        try {
            $crypter = new Crypter('');
            $crypter->encrypt('input');
        } catch (CryptException $e) {
            $this->assertStringContains('Argument $passphrase length must be 56', $e->getMessage());
        }
    }

    function testDecrypt() {
        $crypter = new Crypter(self::PASSPHRASE);

        $this->assertSame('Hello!', $crypter->decrypt('cPpE8Rn0'));
        $this->assertSame('Hello!', $crypter->decrypt('8wE7W4vb5oQ', decode: true));

        try {
            $crypter = new Crypter('');
            $crypter->decrypt('input');
        } catch (CryptException $e) {
            $this->assertStringContains('Argument $passphrase length must be 56', $e->getMessage());
        }
    }
}
