<?php declare(strict_types=1);
namespace test\froq\encrypting;
use froq\encrypting\{Crypt, CryptException};

class CryptTest extends \TestCase
{
    const PASSPHRASE = 'IzARZtq./mU}H|D&y~^Z5~Lr_y}i:Y:-q*Vr]n5-}0ydXcu31\0Nu?[Q';

    function testEncrypt() {
        $this->assertSame('cPpE8Rn0', Crypt::encrypt('Hello!', self::PASSPHRASE));
        $this->assertSame('8wE7W4vb5oQ', Crypt::encrypt('Hello!', self::PASSPHRASE, encode: true));

        try {
            Crypt::encrypt('input', passphrase: '');
        } catch (CryptException $e) {
            $this->assertStringContains('Argument $passphrase length must be 56', $e->getMessage());
        }
    }

    function testDecrypt() {
        $this->assertSame('Hello!', Crypt::decrypt('cPpE8Rn0', self::PASSPHRASE));
        $this->assertSame('Hello!', Crypt::decrypt('8wE7W4vb5oQ', self::PASSPHRASE, decode: true));

        try {
            Crypt::decrypt('input', passphrase: '');
        } catch (CryptException $e) {
            $this->assertStringContains('Argument $passphrase length must be 56', $e->getMessage());
        }
    }
}
