<?php declare(strict_types=1);
namespace test\froq\encrypting;
use froq\encrypting\{Generator, GeneratorException, HashException,
    Suid, SuidException, Token, TokenException};

class GeneratorTest extends \TestCase
{
    function testGenerateToken() {
        $this->assertLength(Token::LENGTH, Generator::generateToken());
        $this->assertLength(32, $token = Generator::generateToken(32));
        $this->assertTrue(ctype_xdigit($token));

        try {
            Generator::generateToken(10);
        } catch (GeneratorException $e) {
            $this->assertStringContains('Invalid length', $e->getMessage());
            $this->assertInstanceOf(TokenException::class, $e->getCause());
            $this->assertInstanceOf(HashException::class, $e->getCause()->getCause());
        }
    }

    function testGenerateSalt() {
        $this->assertLength(Suid::SALT_LENGTH, Generator::generateSalt());
        $this->assertLength(10, $salt = Generator::generateSalt(10));
        $this->assertMatches('~^[a-zA-Z0-9]+$~', $salt);

        try {
            Generator::generateSalt(0);
        } catch (GeneratorException $e) {
            $this->assertStringContains('Argument $length must be greater than 1', $e->getMessage());
            $this->assertInstanceOf(SuidException::class, $e->getCause());
        }
    }

    function testGenerateNonce() {
        $this->assertLength(Suid::NONCE_LENGTH, Generator::generateNonce());
        $this->assertLength(20, $nonce = Generator::generateNonce(20));
        $this->assertMatches('~^[a-f0-9]+$~', $nonce);

        try {
            Generator::generateNonce(0);
        } catch (GeneratorException $e) {
            $this->assertStringContains('Argument $length must be greater than 1', $e->getMessage());
            $this->assertInstanceOf(SuidException::class, $e->getCause());
        }
    }

    function testGenerateUuid() {
        $this->assertLength(36, Generator::generateUuid());
        $this->assertLength(32, Generator::generateUuid(dashed: false));
    }

    function testGenerateGuid() {
        $this->assertLength(36, Generator::generateGuid());
        $this->assertLength(32, Generator::generateGuid(dashed: false));
    }

    function testGenerateSerial() {
        $this->assertLength(20, Generator::generateSerial());
        $this->assertLength(30, Generator::generateSerial(30));

        [$time, $date] = [date('U'), date('Ymd')];

        $this->assertStringStartsWith($time, Generator::generateSerial(dated: false));
        $this->assertStringStartsNotWith($time, Generator::generateSerial(dated: true));

        $this->assertStringStartsWith($date, Generator::generateSerial(dated: true));
        $this->assertStringStartsNotWith($date, Generator::generateSerial(dated: false));

        $this->expectException(GeneratorException::class);
        $this->expectExceptionMessage('Argument $length must be minimun 20, 1 given');
        Generator::generateRandomSerial(1);
    }

    function testGenerateRandomSerial() {
        $this->assertLength(20, Generator::generateRandomSerial());
        $this->assertLength(30, Generator::generateRandomSerial(30));

        $this->expectException(GeneratorException::class);
        $this->expectExceptionMessage('Argument $length must be minimun 20, 1 given');
        Generator::generateRandomSerial(1);
    }

    function testGenerateId() {
        $this->assertLength(10, Generator::generateId(10));
        $this->assertMatches('~^[a-zA-Z0-9]+$~', Generator::generateId(10));
        $this->assertMatches('~^[a-z0-9]+$~', Generator::generateId(10, base: 36));
        $this->assertMatches('~^[a-f0-9]+$~', Generator::generateId(10, base: 16));

        $time = date('U'); $date = date('Ymd');

        $this->assertStringStartsWith($time, Generator::generateId(10, dated: false));
        $this->assertStringStartsNotWith($time, Generator::generateId(10, dated: true));

        $this->assertStringStartsWith($date, Generator::generateId(10, dated: true));
        $this->assertStringStartsNotWith($date, Generator::generateId(10, dated: false));

        try {
            Generator::generateId(1);
        } catch (GeneratorException $e) {
            $this->assertEquals('Argument $length must be minimun 10, 1 given', $e->getMessage());
        }

        try {
            Generator::generateId(10, base: 1);
        } catch (GeneratorException $e) {
            $this->assertEquals('Argument $base must be between 10-62, 1 given', $e->getMessage());
        }
    }

    function testGenerateShortId() {
        $this->assertLength(16, Generator::generateShortId());
        $this->assertStringStartsWith(date('Ymd'), Generator::generateShortId(dated: true));
        $this->assertStringMatchesFormat('%x', Generator::generateShortId(base: 16));
    }

    function testGenerateLongId() {
        $this->assertLength(32, Generator::generateLongId());
        $this->assertStringStartsWith(date('Ymd'), Generator::generateLongId(dated: true));
        $this->assertStringMatchesFormat('%x', Generator::generateLongId(base: 16));
    }

    function testGenerateSerialId() {
        $this->assertLength(20, Generator::generateSerialId());
        $this->assertStringStartsWith(date('Ymd'), Generator::generateSerialId(true));
    }

    function testGenerateRandomId() {
        $this->assertLength(10, Generator::generateRandomId(10));

        try {
            Generator::generateRandomId(1);
        } catch (GeneratorException $e) {
            $this->assertEquals('Argument $length must be minimun 4, 1 given', $e->getMessage());
        }

        try {
            Generator::generateRandomId(10, base: 1);
        } catch (GeneratorException $e) {
            $this->assertEquals('Argument $base must be between 10-62, 1 given', $e->getMessage());
        }
    }

    function testGenerateObjectId() {
        $this->assertLength(24, Generator::generateObjectId());
    }

    function testGeneratePassword() {
        $this->assertLength(16, Generator::generatePassword());
        $this->assertLength(12, Generator::generatePassword(12));
    }

    function testGenerateOneTimePassword() {
        $this->assertLength(6, Generator::generateOneTimePassword('secret'));
        $this->assertLength(10, Generator::generateOneTimePassword('secret', 10));
    }
}
