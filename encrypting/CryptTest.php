<?php declare(strict_types=1);
namespace test\froq\encrypting;
use froq\encrypting\{Crypt, CryptException};

class CryptTest extends \TestCase
{
    const SECRET = 'IzARZtq./mU}H|D&y~^Z5~Lr_y}i:Y:-q*Vr]n5-}0ydXcu31\0Nu?[Q';

    function testEncrypt() {
        $crypt = new Crypt(self::SECRET);
        $this->assertSame('cPpE8Rn0', $crypt->encrypt('Hello!'));

        $crypt = new Crypt(self::SECRET, encdec: true);
        $this->assertSame('8wE7W4vb5oQ', $crypt->encrypt('Hello!'));

        try {
            $crypt = new Crypt('s3cr3t');
            $crypt->encrypt('input');
        } catch (CryptException $e) {
            $this->assertSame('Argument $secret length must be 56 [given length: 6]', $e->getMessage());
        }

        try {
            $crypt = new Crypt(self::SECRET, encdec: 1);
            $crypt->encrypt('input');
        } catch (CryptException $e) {
            $this->assertSame('Argument $encdec must be between 2-62, 1 given', $e->getMessage());
        }
    }

    function testDecrypt() {
        $crypt = new Crypt(self::SECRET);
        $this->assertSame('Hello!', $crypt->decrypt('cPpE8Rn0'));

        $crypt = new Crypt(self::SECRET, encdec: true);
        $this->assertSame('Hello!', $crypt->decrypt('8wE7W4vb5oQ'));

        try {
            $crypt = new Crypt('s3cr3t');
            $crypt->decrypt('input');
        } catch (CryptException $e) {
            $this->assertSame('Argument $secret length must be 56 [given length: 6]', $e->getMessage());
        }

        try {
            $crypt = new Crypt(self::SECRET, encdec: 1);
            $crypt->decrypt('input');
        } catch (CryptException $e) {
            $this->assertSame('Argument $encdec must be between 2-62, 1 given', $e->getMessage());
        }
    }
}
